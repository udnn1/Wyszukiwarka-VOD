#!/usr/bin/env python3
from __future__ import annotations

import argparse
import concurrent.futures
import datetime as dt
import email.utils
import functools
import html
import json
import re
import sys
import unicodedata
import urllib.parse
import urllib.request
import xml.etree.ElementTree as ET
from dataclasses import dataclass
from typing import Any

try:
    from scrapling.parser import Selector
except Exception as exc:
    print(json.dumps({"ok": False, "error": f"Scrapling import failed: {exc}"}))
    sys.exit(1)


UPFLIX_BASE_URL = "https://upflix.pl"
UPFLIX_RECENT_DAYS = 14
UPFLIX_RSS_SCAN_LIMIT = 60
UPFLIX_RSS_URLS = {
    "netflix": "https://upflix.pl/aktualnosci/rss-netflix",
    "hbomax": "https://upflix.pl/aktualnosci/rss-hbomax",
}
SECTION_TYPE_ORDER = ("added", "returned", "episodes", "stop")
UPFLIX_SKIP_TITLE_FRAGMENTS = (
    " na materialach od ",
    " na zwiastunach od ",
    "zwiastun",
    "pierwsze zdjecia",
    "zmierzaja do",
    "data premiery",
    "co wydarzy sie",
)
FILMWEB_BASE_URL = "https://www.filmweb.pl"

HTTP_HEADERS = {
    "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,application/rss+xml;q=0.9,*/*;q=0.8",
    "Accept-Language": "pl-PL,pl;q=0.9,en-US;q=0.8,en;q=0.7",
    "Cache-Control": "no-cache",
    "Pragma": "no-cache",
    "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Safari/537.36",
}


def json_ok(payload: dict[str, Any]) -> None:
    print(json.dumps({"ok": True, "payload": payload}, separators=(",", ":")))


def json_error(message: str) -> None:
    print(json.dumps({"ok": False, "error": message}, separators=(",", ":")))


def fetch_text(url: str, timeout: int = 25) -> str:
    request = urllib.request.Request(url, headers=HTTP_HEADERS)

    with urllib.request.urlopen(request, timeout=timeout) as response:
        status = getattr(response, "status", response.getcode())

        if status < 200 or status >= 300:
            raise RuntimeError(f"HTTP {status} for {url}")

        raw = response.read()
        charset = response.headers.get_content_charset() or "utf-8"
        text = raw.decode(charset, errors="replace")

    if not text.strip():
        raise RuntimeError(f"Empty response for {url}")

    return text


def clean_text(value: Any) -> str:
    text = html.unescape(str(value or ""))
    text = text.replace("\xa0", " ")
    return re.sub(r"\s+", " ", text).strip()


def strip_accents(value: str) -> str:
    value = value.translate(
        str.maketrans(
            {
                "ą": "a",
                "ć": "c",
                "ę": "e",
                "ł": "l",
                "ń": "n",
                "ó": "o",
                "ś": "s",
                "ź": "z",
                "ż": "z",
                "Ą": "A",
                "Ć": "C",
                "Ę": "E",
                "Ł": "L",
                "Ń": "N",
                "Ó": "O",
                "Ś": "S",
                "Ź": "Z",
                "Ż": "Z",
            }
        )
    )
    normalized = unicodedata.normalize("NFKD", value)
    return normalized.encode("ascii", "ignore").decode("ascii")


def normalize_key(value: str) -> str:
    value = strip_accents(clean_text(value)).lower()
    return re.sub(r"[^a-z0-9]+", " ", value).strip()


def clean_title(value: str) -> str:
    value = clean_text(value)
    value = re.sub(r"\s*\[[^\]]+\]", " ", value)
    return clean_text(value)


def extract_note_from_title(value: str) -> str:
    notes = []

    for note in re.findall(r"\[([^\]]+)\]", value):
        note = clean_text(note)

        if not note:
            continue

        lower = normalize_key(note)

        if (
            "audio" in lower
            or "napisy" in lower
            or "odcinek" in lower
            or "odcinki" in lower
            or re.search(r"s\d{1,2}e\d{1,3}", lower, re.I)
        ):
            continue

        if note not in notes:
            notes.append(note)

    return ", ".join(notes)


def absolute_url(base_url: str, href: str) -> str:
    href = clean_text(href)

    if not href:
        return ""

    return urllib.parse.urljoin(base_url, href)


def absolute_upflix_url(href: str) -> str:
    return absolute_url(UPFLIX_BASE_URL, href)


def absolute_filmweb_url(href: str) -> str:
    return absolute_url(FILMWEB_BASE_URL, href)


def upflix_catalog_key_from_url(url: str) -> str:
    path = urllib.parse.urlparse(url).path.strip("/")

    if not path:
        return ""

    match = re.match(r"^(?:film|serial)/zobacz/([^/?#]+)", path, re.I)

    if match:
        return match.group(1).lower()

    return ""


def usable_upflix_poster_url(url: str) -> str:
    url = absolute_upflix_url(url)

    if not url or url.startswith("data:"):
        return ""

    if "no-poster" in url.lower():
        return ""

    return url


def poster_title_keys_from_text(value: str) -> list[str]:
    keys = []

    for title in re.split(r"\s*/\s*", clean_text(value)):
        key = normalize_key(clean_title(title))

        if key and key not in keys:
            keys.append(key)

    return keys


def media_type_from_url(url: str) -> str | None:
    lowered = url.lower()

    if "/film/" in lowered:
        return "movie"

    if "/serial/" in lowered:
        return "tv"

    return None


def media_label_from_type(media_type: str | None) -> str:
    if media_type == "movie":
        return "Film"

    if media_type == "tv":
        return "Serial"

    return ""


def parse_date_iso(value: str) -> str | None:
    value = clean_text(value)

    if not value:
        return None

    try:
        parsed = dt.datetime.fromisoformat(value.replace("Z", "+00:00"))
        return parsed.date().isoformat()
    except ValueError:
        pass

    try:
        parsed = email.utils.parsedate_to_datetime(value)
        return parsed.date().isoformat()
    except (TypeError, ValueError, IndexError, OverflowError):
        return None


def display_date(iso_date: str | None) -> str:
    if not iso_date or not re.match(r"^\d{4}-\d{2}-\d{2}$", iso_date):
        return ""

    try:
        return dt.date.fromisoformat(iso_date).strftime("%d.%m.%Y")
    except ValueError:
        return ""


def recent_threshold_date() -> str:
    return (dt.date.today() - dt.timedelta(days=UPFLIX_RECENT_DAYS - 1)).isoformat()


def article_is_recent(value: str | None) -> bool:
    if not value or not re.match(r"^\d{4}-\d{2}-\d{2}$", value):
        return True

    return value >= recent_threshold_date()


def article_year(article_date: str | None) -> int:
    if article_date and re.match(r"^\d{4}-\d{2}-\d{2}$", article_date):
        return int(article_date[:4])

    return dt.date.today().year


def polish_month_number(month_name: str) -> int | None:
    return {
        "stycznia": 1,
        "styczen": 1,
        "lutego": 2,
        "luty": 2,
        "marca": 3,
        "marzec": 3,
        "kwietnia": 4,
        "kwiecien": 4,
        "maja": 5,
        "maj": 5,
        "czerwca": 6,
        "czerwiec": 6,
        "lipca": 7,
        "lipiec": 7,
        "sierpnia": 8,
        "sierpien": 8,
        "wrzesnia": 9,
        "wrzesien": 9,
        "pazdziernika": 10,
        "pazdziernik": 10,
        "listopada": 11,
        "listopad": 11,
        "grudnia": 12,
        "grudzien": 12,
    }.get(normalize_key(month_name))


def parse_polish_date_from_text(value: str, fallback_year_date: str | None = None) -> str | None:
    value = clean_text(value)

    if not value:
        return None

    match = re.search(r"\b(20\d{2}|19\d{2})-(\d{2})-(\d{2})\b", value)

    if match:
        try:
            return dt.date(int(match.group(1)), int(match.group(2)), int(match.group(3))).isoformat()
        except ValueError:
            pass

    match = re.search(r"\b(\d{1,2})[.\-/](\d{1,2})[.\-/](20\d{2}|19\d{2})\b", value)

    if match:
        try:
            return dt.date(int(match.group(3)), int(match.group(2)), int(match.group(1))).isoformat()
        except ValueError:
            pass

    match = re.search(r"\b(\d{1,2})\s+([^\W\d_]+)(?:\s+(20\d{2}|19\d{2}))?\b", value, re.I | re.U)

    if match:
        month = polish_month_number(match.group(2))
        year = int(match.group(3)) if match.group(3) else article_year(fallback_year_date)

        if month is not None:
            try:
                return dt.date(year, month, int(match.group(1))).isoformat()
            except ValueError:
                pass

    return None


def cell_is_date_like(value: str, article_date: str | None) -> bool:
    return parse_polish_date_from_text(value, article_date) is not None


def selector_all_text(selector: Any) -> str:
    try:
        return clean_text(selector.get_all_text())
    except Exception:
        try:
            return clean_text(" ".join(selector.xpath(".//text()").getall()))
        except Exception:
            return clean_text(selector.get())


def parse_rss_items(rss_xml: str, limit: int = UPFLIX_RSS_SCAN_LIMIT) -> list[dict[str, Any]]:
    root = ET.fromstring(rss_xml)
    items = []

    for item in root.findall("./channel/item"):
        link = clean_text(item.findtext("link", ""))

        if not link:
            continue

        pub_date = clean_text(item.findtext("pubDate", ""))
        items.append(
            {
                "title": clean_text(item.findtext("title", "")),
                "url": absolute_upflix_url(link),
                "publishedAt": parse_date_iso(pub_date),
                "publishedRaw": pub_date,
            }
        )

        if len(items) >= limit:
            break

    if not items:
        raise RuntimeError("RSS Upflix has no article links.")

    return items


def extract_article_date_from_html(article_html: str, fallback: str | None) -> str | None:
    page = Selector(article_html)

    for selector in (
        'meta[property="article:published_time"]::attr(content)',
        'meta[name="article:published_time"]::attr(content)',
        'time::attr(datetime)',
    ):
        value = page.css(selector).get()
        parsed = parse_date_iso(value or "")

        if parsed is not None:
            return parsed

    top_text = selector_all_text(page)[:8000]
    return parse_polish_date_from_text(top_text, fallback) or fallback


def section_type_from_heading(heading: str) -> str | None:
    normalized = normalize_key(heading)

    if not normalized:
        return None

    if (
        "dodane tytuly" in normalized
        or "dodano tytuly" in normalized
        or "dodane tytulow" in normalized
        or "dodano tytulow" in normalized
        or "nowo dodane" in normalized
        or "nowosci dodane" in normalized
        or "pelna lista tytulow" in normalized
        or ("dodane" in normalized and "tytul" in normalized and "odcink" not in normalized and "usun" not in normalized)
    ):
        return "added"

    if (
        "ponownie dodane" in normalized
        or "ponownie dodano" in normalized
        or "powracaja" in normalized
        or "wrocily" in normalized
        or "wracaja" in normalized
    ):
        return "returned"

    if (
        "nowe odcinki" in normalized
        or "dodane odcinki" in normalized
        or "uzupelnione tlumaczenia" in normalized
        or "nowe sezony" in normalized
    ):
        return "episodes"

    if (
        "zaplanowano usuniecie" in normalized
        or "usunieto" in normalized
        or "usuniete tytuly" in normalized
        or "koniec licencji" in normalized
        or "zobacz takze" in normalized
    ):
        return "stop"

    return None


def section_label(section_type: str) -> str:
    if section_type == "added":
        return "Dodane tytu\u0142y"

    if section_type == "returned":
        return "Ponownie dodane"

    return "Nowo\u015b\u0107"


def article_should_skip_scan(article: dict[str, Any]) -> bool:
    title = normalize_key(str(article.get("title") or ""))

    if not title:
        return False

    return any(fragment.strip() in title for fragment in UPFLIX_SKIP_TITLE_FRAGMENTS)


def ordered_section_types(section_types: set[str]) -> list[str]:
    ordered = [section_type for section_type in SECTION_TYPE_ORDER if section_type in section_types]
    ordered.extend(sorted(section_types.difference(SECTION_TYPE_ORDER)))

    return ordered


def article_section_types_seen(article_html: str) -> list[str]:
    page = Selector(article_html)
    section_types = set()

    for element in page.css("h1,h2,h3,h4,h5,h6,p,strong,b"):
        section_type = section_type_from_heading(selector_all_text(element))

        if section_type is not None:
            section_types.add(section_type)

    return ordered_section_types(section_types)


def extract_year_from_cells(cells: list[str]) -> int | None:
    for cell in reversed(cells):
        match = re.search(r"\b(19|20)\d{2}\b", cell)

        if match:
            return int(match.group(0))

    return None


def extract_rating_from_cells(cells: list[str]) -> str:
    for cell in cells:
        cell = cell.strip()

        if re.match(r"^\d(?:[,.]\d)?$", cell):
            return cell.replace(",", ".")

    return ""


def table_rows(table_html: str) -> list[list[dict[str, Any]]]:
    table = Selector(table_html)
    rows = []

    for row in table.css("tr"):
        cells = []

        for cell in row.css("td, th"):
            cells.append(
                {
                    "text": selector_all_text(cell),
                    "href": absolute_upflix_url(cell.css("a::attr(href)").get() or ""),
                }
            )

        if cells:
            rows.append(cells)

    return rows


def table_has_title_header(table_html: str) -> bool:
    rows = table_rows(table_html)

    if not rows:
        return False

    first_row = " ".join(cell["text"] for cell in rows[0])
    normalized = normalize_key(first_row)
    return "tytul" in normalized or "ocena" in normalized or "rok" in normalized or "produkcja" in normalized


def parse_table_items(table_html: str, section_type: str, article_url: str, article_date: str | None) -> list[dict[str, Any]]:
    rows = table_rows(table_html)
    items = []

    for row_index, row_cells in enumerate(rows):
        cells = [cell["text"] for cell in row_cells]

        if not cells:
            continue

        row_text = normalize_key(" ".join(cells))

        if row_index == 0 and ("tytul" in row_text or "ocena" in row_text):
            continue

        raw_title = cells[0] if cells else ""
        title = clean_title(raw_title)

        if not title or normalize_key(title) == "tytul":
            continue

        url = row_cells[0]["href"] if row_cells else ""
        second_cell = cells[1] if len(cells) > 1 else ""
        raw_original_title = ""

        if (
            second_cell
            and not cell_is_date_like(second_cell, article_date)
            and not re.match(r"^\d(?:[,.]\d)?$", second_cell)
            and not re.match(r"^(19|20)\d{2}$", second_cell)
        ):
            raw_original_title = second_cell

        original_title = clean_title(raw_original_title)

        if normalize_key(original_title) == normalize_key(title):
            original_title = ""

        media_type = media_type_from_url(url)

        items.append(
            {
                "title": title,
                "originalTitle": original_title,
                "year": extract_year_from_cells(cells),
                "rating": extract_rating_from_cells(cells),
                "section": section_label(section_type),
                "sectionType": section_type,
                "mediaType": media_type,
                "mediaLabel": media_label_from_type(media_type),
                "note": extract_note_from_title(raw_title),
                "url": url or article_url,
                "articleUrl": article_url,
                "addedDate": article_date,
                "addedDateDisplay": display_date(article_date),
            }
        )

    return items


def article_poster_maps(article_html: str) -> dict[str, dict[str, str]]:
    page = Selector(article_html)
    maps: dict[str, dict[str, str]] = {
        "byUrl": {},
        "byTitle": {},
    }

    for anchor in page.css("a"):
        url_key = upflix_catalog_key_from_url(absolute_upflix_url(anchor.css("::attr(href)").get() or ""))

        if not url_key:
            continue

        image_sources = []

        for attribute in ("data-src", "data-original", "data-lazy", "src"):
            image_sources.extend(anchor.css(f"img::attr({attribute})").getall())

        poster = ""

        for source in image_sources:
            poster = usable_upflix_poster_url(source)

            if poster:
                break

        if not poster:
            continue

        if url_key not in maps["byUrl"]:
            maps["byUrl"][url_key] = poster

        for alt in anchor.css("img::attr(alt)").getall():
            for title_key in poster_title_keys_from_text(alt):
                maps["byTitle"].setdefault(title_key, poster)

    return maps


def apply_article_posters_to_items(items: list[dict[str, Any]], article_html: str) -> list[dict[str, Any]]:
    if not items:
        return items

    try:
        maps = article_poster_maps(article_html)
    except Exception:
        return items

    for item in items:
        if item.get("poster"):
            continue

        url_key = upflix_catalog_key_from_url(str(item.get("url") or ""))
        poster = maps["byUrl"].get(url_key, "") if url_key else ""

        if not poster:
            for title in (str(item.get("title") or ""), str(item.get("originalTitle") or "")):
                title_key = normalize_key(title)

                if title_key and title_key in maps["byTitle"]:
                    poster = maps["byTitle"][title_key]
                    break

        if poster:
            item["poster"] = poster

    return items


def element_html(element: Any) -> str:
    try:
        from lxml import etree

        return etree.tostring(element, encoding="unicode", method="html")
    except Exception:
        return ""


def element_text(element: Any) -> str:
    try:
        return clean_text(" ".join(element.itertext()))
    except Exception:
        return ""


def element_tag(element: Any) -> str:
    return str(getattr(element, "tag", "")).lower()


def element_can_be_section_heading(element: Any) -> bool:
    tag = element_tag(element)
    return bool(re.match(r"^h[1-6]$", tag)) or tag in {"p", "strong", "b"}


def content_roots(page: Any) -> list[Any]:
    roots = []

    for root in page.css(".news-content"):
        if hasattr(root, "_root"):
            roots.append(root._root)

    if roots:
        return roots

    bodies = page.css("body")

    if bodies and hasattr(bodies[0], "_root"):
        return [bodies[0]._root]

    return [page._root] if hasattr(page, "_root") else []


def collect_items_from_element(
    element: Any,
    current_section: str | None,
    article_url: str,
    article_date: str | None,
    items: list[dict[str, Any]],
) -> str | None:
    for child in list(element):
        tag = element_tag(child)

        if tag in {"script", "style", "noscript"}:
            continue

        if tag == "table":
            if current_section in {"added", "returned"}:
                table_html = element_html(child)

                if table_html and table_has_title_header(table_html):
                    items.extend(parse_table_items(table_html, current_section, article_url, article_date))

            continue

        if element_can_be_section_heading(child):
            section_type = section_type_from_heading(element_text(child))

            if section_type is not None:
                current_section = section_type

        current_section = collect_items_from_element(child, current_section, article_url, article_date, items)

    return current_section


def parse_article_items_dom(article_html: str, article_url: str, article_date: str | None) -> list[dict[str, Any]]:
    page = Selector(article_html)
    items: list[dict[str, Any]] = []

    for root in content_roots(page):
        collect_items_from_element(root, None, article_url, article_date, items)

    return items


def parse_article_items_by_heading_segments(article_html: str, article_url: str, article_date: str | None) -> list[dict[str, Any]]:
    heading_pattern = re.compile(r"<(h[1-6]|strong|b)\b[^>]*>(.*?)</\1>", re.I | re.S)
    headings = []

    for match in heading_pattern.finditer(article_html):
        heading_text = clean_text(re.sub(r"<[^>]+>", " ", match.group(2)))
        section_type = section_type_from_heading(heading_text)

        if section_type is not None:
            headings.append({"start": match.start(), "end": match.end(), "sectionType": section_type})

    items: list[dict[str, Any]] = []

    for index, heading in enumerate(headings):
        section_type = heading["sectionType"]

        if section_type not in {"added", "returned"}:
            continue

        segment_end = headings[index + 1]["start"] if index + 1 < len(headings) else len(article_html)
        segment = article_html[heading["end"] : segment_end]

        for table_match in re.finditer(r"<table\b[^>]*>.*?</table>", segment, re.I | re.S):
            table_html = table_match.group(0)

            if table_has_title_header(table_html):
                items.extend(parse_table_items(table_html, section_type, article_url, article_date))

    return items


def dedupe_items(items: list[dict[str, Any]]) -> list[dict[str, Any]]:
    seen = set()
    unique = []

    for item in items:
        key = "|".join(
            [
                normalize_key(str(item.get("title") or "")),
                normalize_key(str(item.get("originalTitle") or "")),
                str(item.get("year") or ""),
                str(item.get("sectionType") or ""),
                str(item.get("addedDate") or ""),
            ]
        )

        if key == "||||" or key in seen:
            continue

        seen.add(key)
        unique.append(item)

    return unique


def parse_article_items(article_html: str, article_url: str, article_date: str | None) -> list[dict[str, Any]]:
    items: list[dict[str, Any]] = []

    try:
        items.extend(parse_article_items_dom(article_html, article_url, article_date))
    except Exception:
        pass

    try:
        items.extend(parse_article_items_by_heading_segments(article_html, article_url, article_date))
    except Exception:
        pass

    return apply_article_posters_to_items(dedupe_items(items), article_html)


def sort_items_latest_first(items: list[dict[str, Any]]) -> list[dict[str, Any]]:
    section_order = {"added": 0, "returned": 1}

    def compare(left: dict[str, Any], right: dict[str, Any]) -> int:
        left_date = str(left.get("addedDate") or "")
        right_date = str(right.get("addedDate") or "")

        if left_date != right_date:
            return -1 if left_date > right_date else 1

        left_section = section_order.get(str(left.get("sectionType") or ""), 99)
        right_section = section_order.get(str(right.get("sectionType") or ""), 99)

        if left_section != right_section:
            return left_section - right_section

        left_title = normalize_key(str(left.get("title") or ""))
        right_title = normalize_key(str(right.get("title") or ""))

        if left_title == right_title:
            return 0

        return -1 if left_title < right_title else 1

    return sorted(items, key=functools.cmp_to_key(compare))


@dataclass
class ArticleResult:
    article: dict[str, Any]
    items: list[dict[str, Any]]


def parse_upflix_article(article: dict[str, Any]) -> ArticleResult:
    article_html = fetch_text(str(article["url"]))
    article_date = extract_article_date_from_html(article_html, article.get("publishedAt"))
    article = dict(article)
    article["publishedAt"] = article_date
    article["sectionTypesSeen"] = article_section_types_seen(article_html)

    if not article_is_recent(article_date):
        return ArticleResult(article=article, items=[])

    items = parse_article_items(article_html, str(article["url"]), article_date)

    for item in items:
        item["sourceArticleTitle"] = article.get("title") or ""
        item["sourceArticleUrl"] = article.get("url") or ""

    return ArticleResult(article=article, items=items)


def build_upflix_payload(platform: str, limit: int) -> dict[str, Any]:
    rss_url = UPFLIX_RSS_URLS.get(platform)

    if not rss_url:
        raise RuntimeError("Unsupported Upflix platform.")

    rss_xml = fetch_text(rss_url)
    articles = parse_rss_items(rss_xml, UPFLIX_RSS_SCAN_LIMIT)
    recent_articles = []
    skipped_articles = []

    for article in articles:
        if not article_is_recent(article.get("publishedAt")):
            break

        if article_should_skip_scan(article):
            skipped_articles.append(
                {
                    "title": article.get("title") or "",
                    "url": article.get("url") or "",
                    "publishedAt": article.get("publishedAt"),
                    "itemsFound": 0,
                    "skipped": True,
                }
            )
            continue

        recent_articles.append(article)

    scanned_articles = []
    all_items: list[dict[str, Any]] = []
    section_types_seen: set[str] = set()
    last_error = None

    with concurrent.futures.ThreadPoolExecutor(max_workers=min(8, max(1, len(recent_articles)))) as executor:
        futures = [executor.submit(parse_upflix_article, article) for article in recent_articles]

        for index, future in enumerate(futures):
            article = recent_articles[index]

            try:
                result = future.result()

                if not article_is_recent(result.article.get("publishedAt")):
                    break

                article_section_types = [
                    str(section_type)
                    for section_type in result.article.get("sectionTypesSeen", [])
                    if section_type
                ]
                section_types_seen.update(article_section_types)

                scanned_articles.append(
                    {
                        "title": result.article.get("title") or "",
                        "url": result.article.get("url") or "",
                        "publishedAt": result.article.get("publishedAt"),
                        "itemsFound": len(result.items),
                        "sectionTypesSeen": article_section_types,
                    }
                )
                all_items.extend(result.items)
            except Exception as exc:
                last_error = str(exc)
                scanned_articles.append(
                    {
                        "title": article.get("title") or "",
                        "url": article.get("url") or "",
                        "publishedAt": article.get("publishedAt"),
                        "itemsFound": 0,
                        "error": last_error,
                    }
                )

    all_items = sort_items_latest_first(dedupe_items(all_items))
    total_count = len(all_items)

    if limit > 0:
        all_items = all_items[:limit]

    if total_count == 0:
        message = f"Brak nowo dodanych lub ponownie dodanych pozycji w ostatnich {UPFLIX_RECENT_DAYS} dniach."

        if last_error:
            message += f" Ostatni blad: {last_error}"

        raise RuntimeError(message)

    return {
        "platform": platform,
        "source": "upflix",
        "engine": "scrapling",
        "rangeDays": UPFLIX_RECENT_DAYS,
        "scannedArticles": scanned_articles,
        "scannedCount": len(scanned_articles),
        "skippedArticles": skipped_articles,
        "skippedCount": len(skipped_articles),
        "sectionTypesSeen": ordered_section_types(section_types_seen),
        "items": all_items,
        "count": len(all_items),
        "totalCount": total_count,
        "generatedAt": dt.datetime.now(dt.timezone.utc).astimezone().isoformat(),
    }


def filmweb_month_name(month: int) -> str:
    names = {
        1: "stycze\u0144",
        2: "luty",
        3: "marzec",
        4: "kwiecie\u0144",
        5: "maj",
        6: "czerwiec",
        7: "lipiec",
        8: "sierpie\u0144",
        9: "wrzesie\u0144",
        10: "pa\u017adziernik",
        11: "listopad",
        12: "grudzie\u0144",
    }
    return names.get(month, "")


def filmweb_premiere_page_url(kind: str, year: int, month: int) -> str:
    if kind == "tv":
        return f"{FILMWEB_BASE_URL}/serials/premiere/{year}/{month}"

    return f"{FILMWEB_BASE_URL}/premiere/{year}/{month}"


def filmweb_title_type_from_kind(kind: str) -> str:
    return "serial" if kind == "tv" else "film"


def filmweb_premiere_date_from_text(text: str, year: int, month: int) -> str | None:
    text = clean_text(text)

    if not text:
        return None

    match = re.search(r"\b(\d{1,2})\s+([^\W\d_]+)\b", text, re.I | re.U)

    if not match:
        return None

    found_month = polish_month_number(match.group(2))

    if found_month != month:
        return None

    try:
        return dt.date(year, month, int(match.group(1))).isoformat()
    except ValueError:
        return None


def filmweb_premiere_date_from_attrs(element: Any, year: int, month: int) -> str | None:
    attrs = getattr(element, "attrib", {}) or {}

    for attr_name in ("title", "datetime", "data-date", "data-day", "content"):
        value = clean_text(attrs.get(attr_name, ""))

        if not value:
            continue

        match = re.search(r"\b(20\d{2}|19\d{2})-(\d{2})-(\d{2})\b", value)

        if not match:
            continue

        found_year = int(match.group(1))
        found_month = int(match.group(2))
        found_day = int(match.group(3))

        if found_year != year or found_month != month:
            continue

        try:
            return dt.date(found_year, found_month, found_day).isoformat()
        except ValueError:
            continue

    return None


def filmweb_poster_near_element(element: Any) -> str | None:
    node = element

    for _ in range(5):
        if node is None:
            break

        try:
            images = list(node.iter("img"))
        except Exception:
            images = []

        for image in images:
            src = clean_text(image.attrib.get("src", "") or image.attrib.get("data-src", ""))

            if src:
                return absolute_filmweb_url(src)

        try:
            node = node.getparent()
        except Exception:
            node = None

    return None


def filmweb_preview_poster(box: Any) -> str | None:
    for selector in (
        "[data-image]::attr(data-image)",
        "img::attr(src)",
        "img::attr(data-src)",
    ):
        value = clean_text(box.css(selector).get() or "")

        if value:
            return absolute_filmweb_url(value)

    return None


def filmweb_title_from_anchor(anchor: Any, fallback_href: str) -> str:
    title = clean_text(anchor.css("::attr(title)").get() or "")

    if not title:
        title = selector_all_text(anchor)

    if normalize_key(title) in {"premiera", "sezon", "odcinki", "lista odcinkow"}:
        title = ""

    if title:
        return title

    path = urllib.parse.urlparse(fallback_href).path
    match = re.search(r"/(?:film|serial)/(.+)-\d{4}-\d+", path)

    if not match:
        return ""

    slug = urllib.parse.unquote_plus(match.group(1))
    return clean_text(slug.replace("+", " "))


def filmweb_box_primary_link(box: Any, expected_type: str) -> tuple[str, str, int, int] | None:
    pattern = re.compile(rf"^/{re.escape(expected_type)}/[^\"']+-(\d{{4}})-(\d+)(?:/)?$")

    for anchor in box.css("a"):
        href = clean_text(anchor.css("::attr(href)").get() or "")
        match = pattern.search(href)

        if not match:
            continue

        title = filmweb_title_from_anchor(anchor, href)

        if not title:
            continue

        return title, absolute_filmweb_url(href), int(match.group(1)), int(match.group(2))

    return None


def filmweb_box_detail_url(box: Any, expected_type: str, item_id: int, fallback_url: str) -> str:
    pattern = re.compile(rf"^/{re.escape(expected_type)}/[^\"']+-\d{{4}}-{item_id}/(?:season/\d+|episode/list)(?:/)?$")

    for anchor in box.css(".boxBadge a, a"):
        href = clean_text(anchor.css("::attr(href)").get() or "")

        if pattern.search(href):
            return absolute_filmweb_url(href)

    return fallback_url


def build_filmweb_premieres_payload(kind: str, year: int, month: int) -> dict[str, Any]:
    kind = "tv" if kind == "tv" else "movie"
    source_url = filmweb_premiere_page_url(kind, year, month)
    page = Selector(fetch_text(source_url, timeout=25))
    expected_type = filmweb_title_type_from_kind(kind)
    items_by_key: dict[tuple[int, str], dict[str, Any]] = {}

    for day in page.css(".premieresList__dayRange"):
        premiere_date = clean_text(day.css("::attr(data-date)").get() or "")

        if not re.match(r"^\d{4}-\d{2}-\d{2}$", premiere_date):
            continue

        if not premiere_date.startswith(f"{year:04d}-{month:02d}-"):
            continue

        for box in day.css(".premieresList__box"):
            primary_link = filmweb_box_primary_link(box, expected_type)

            if primary_link is None:
                continue

            title, primary_url, item_year, item_id = primary_link
            detail_url = filmweb_box_detail_url(box, expected_type, item_id, primary_url)
            item = {
                "id": item_id,
                "mediaType": kind,
                "filmwebType": expected_type,
                "title": title,
                "originalTitle": "",
                "englishTitle": "",
                "year": item_year,
                "premiereDate": premiere_date,
                "releaseLabel": display_date(premiere_date),
                "genres": [],
                "overview": "",
                "poster": filmweb_preview_poster(box),
                "filmwebUrl": detail_url,
                "url": detail_url,
            }

            items_by_key[(item_id, premiere_date)] = item

    items = sorted(
        items_by_key.values(),
        key=lambda item: (str(item.get("premiereDate") or "9999-12-31"), str(item.get("title") or "")),
    )

    return {
        "source": "filmweb",
        "engine": "scrapling",
        "url": source_url,
        "sourceUrl": source_url,
        "kind": kind,
        "year": year,
        "month": month,
        "monthKey": f"{year:04d}-{month:02d}",
        "monthLabel": f"{filmweb_month_name(month)} {year}",
        "items": items,
    }


def parse_args(argv: list[str]) -> argparse.Namespace:
    parser = argparse.ArgumentParser()
    subparsers = parser.add_subparsers(dest="mode", required=True)

    upflix = subparsers.add_parser("upflix")
    upflix.add_argument("--platform", required=True)
    upflix.add_argument("--limit", type=int, default=300)

    filmweb = subparsers.add_parser("filmweb-premieres")
    filmweb.add_argument("--kind", default="movie")
    filmweb.add_argument("--year", type=int, required=True)
    filmweb.add_argument("--month", type=int, required=True)

    return parser.parse_args(argv)


def main(argv: list[str]) -> int:
    args = parse_args(argv)

    try:
        if args.mode == "upflix":
            json_ok(build_upflix_payload(args.platform, max(1, min(300, args.limit))))
            return 0

        if args.mode == "filmweb-premieres":
            json_ok(build_filmweb_premieres_payload(args.kind, args.year, args.month))
            return 0

        raise RuntimeError("Unsupported mode.")
    except Exception as exc:
        json_error(str(exc))
        return 1


if __name__ == "__main__":
    raise SystemExit(main(sys.argv[1:]))
