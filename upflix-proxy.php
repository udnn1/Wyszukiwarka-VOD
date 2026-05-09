<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . DIRECTORY_SEPARATOR . 'scrapling-bridge.php';

const UPFLIX_BASE_URL = 'https://upflix.pl';
const UPFLIX_RECENT_DAYS = 14;
const UPFLIX_RSS_SCAN_LIMIT = 60;
const UPFLIX_ALLOWED_PLATFORMS = ['netflix', 'hbomax'];

const UPFLIX_RSS_URLS = [
    'netflix' => 'https://upflix.pl/aktualnosci/rss-netflix',
    'hbomax' => 'https://upflix.pl/aktualnosci/rss-hbomax',
];

function json_response(int $statusCode, array $payload): never
{
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function request_value(string $key): string
{
    $value = $_GET[$key] ?? '';

    return is_string($value) ? trim($value) : '';
}

function request_limit(): int
{
    $raw = request_value('limit');

    if ($raw === '' || !ctype_digit($raw)) {
        return 300;
    }

    return max(1, min(300, (int) $raw));
}

function http_get(string $url): string
{
    $headers = [
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,application/rss+xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
        'Accept-Language: pl-PL,pl;q=0.9,en-US;q=0.8,en;q=0.7',
        'Cache-Control: no-cache',
        'Pragma: no-cache',
        'Referer: https://upflix.pl/',
        'Upgrade-Insecure-Requests: 1',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Safari/537.36',
    ];

    if (function_exists('curl_init')) {
        $curlHandle = curl_init($url);

        if ($curlHandle === false) {
            throw new RuntimeException('Nie udało się zainicjować połączenia HTTP.');
        }

        curl_setopt_array($curlHandle, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 25,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_ENCODING => '',
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);

        $body = curl_exec($curlHandle);

        if ($body === false) {
            $errorMessage = curl_error($curlHandle);
            curl_close($curlHandle);
            throw new RuntimeException('Nie udało się pobrać danych z Upflix: ' . $errorMessage);
        }

        $statusCode = (int) curl_getinfo($curlHandle, CURLINFO_RESPONSE_CODE);
        curl_close($curlHandle);

        if ($statusCode < 200 || $statusCode >= 300) {
            throw new RuntimeException('Upflix zwrócił błąd HTTP ' . $statusCode . ' dla adresu: ' . $url);
        }

        if (trim((string) $body) === '') {
            throw new RuntimeException('Upflix zwrócił pustą odpowiedź dla adresu: ' . $url);
        }

        return (string) $body;
    }

    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 25,
            'ignore_errors' => true,
            'header' => implode("\r\n", $headers),
        ],
    ]);

    $body = @file_get_contents($url, false, $context);

    if (!is_string($body) || trim($body) === '') {
        throw new RuntimeException('Nie udało się pobrać danych z Upflix z poziomu serwera.');
    }

    return $body;
}

function clean_text(string $value): string
{
    $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $value = str_replace(["\xc2\xa0", '&nbsp;'], ' ', $value);
    $value = preg_replace('/\s+/u', ' ', $value) ?? $value;

    return trim($value);
}

function replace_polish_chars(string $value): string
{
    return strtr($value, [
        'ą' => 'a',
        'ć' => 'c',
        'ę' => 'e',
        'ł' => 'l',
        'ń' => 'n',
        'ó' => 'o',
        'ś' => 's',
        'ź' => 'z',
        'ż' => 'z',
        'Ą' => 'A',
        'Ć' => 'C',
        'Ę' => 'E',
        'Ł' => 'L',
        'Ń' => 'N',
        'Ó' => 'O',
        'Ś' => 'S',
        'Ź' => 'Z',
        'Ż' => 'Z',
    ]);
}

function normalize_key(string $value): string
{
    $value = clean_text($value);
    $value = replace_polish_chars($value);

    if (function_exists('iconv')) {
        $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);

        if (is_string($converted) && $converted !== '') {
            $value = $converted;
        }
    }

    $value = replace_polish_chars($value);
    $value = strtolower($value);
    $value = preg_replace('/[^a-z0-9]+/', ' ', $value) ?? '';

    return trim($value);
}

function clean_title(string $value): string
{
    $value = clean_text($value);

    if ($value === '') {
        return '';
    }

    $value = preg_replace('/\s*\[[^\]]+\]/u', ' ', $value) ?? $value;
    $value = preg_replace('/\s+/u', ' ', $value) ?? $value;

    return trim($value);
}

function extract_note_from_title(string $value): string
{
    if (preg_match_all('/\[([^\]]+)\]/u', $value, $matches) !== 1) {
        return '';
    }

    $notes = [];

    foreach ($matches[1] as $match) {
        $note = clean_text($match);

        if ($note === '') {
            continue;
        }

        $lower = function_exists('mb_strtolower') ? mb_strtolower($note, 'UTF-8') : strtolower($note);

        if (
            str_contains($lower, '+ audio')
            || str_contains($lower, '+ napisy')
            || str_contains($lower, 'napisy')
            || str_contains($lower, 'audio')
            || str_contains($lower, 'odcinek')
            || str_contains($lower, 'odcinki')
            || str_contains($lower, 'odcinków')
            || preg_match('/s\d{1,2}e\d{1,3}/iu', $lower) === 1
        ) {
            continue;
        }

        $notes[] = $note;
    }

    return implode(', ', array_values(array_unique($notes)));
}

function absolute_upflix_url(string $href): string
{
    $href = trim($href);

    if ($href === '') {
        return '';
    }

    if (preg_match('#^https?://#i', $href) === 1) {
        return $href;
    }

    if (str_starts_with($href, '//')) {
        return 'https:' . $href;
    }

    if (!str_starts_with($href, '/')) {
        $href = '/' . $href;
    }

    return UPFLIX_BASE_URL . $href;
}

function media_type_from_url(string $url): ?string
{
    $normalized = strtolower($url);

    if (str_contains($normalized, '/film/')) {
        return 'movie';
    }

    if (str_contains($normalized, '/serial/')) {
        return 'tv';
    }

    return null;
}

function media_type_from_cells(array $cells, string $url): ?string
{
    $fromUrl = media_type_from_url($url);

    if ($fromUrl !== null) {
        return $fromUrl;
    }

    foreach ($cells as $cell) {
        $normalized = normalize_key((string) $cell);

        if (in_array($normalized, ['film', 'filmy', 'movie', 'movies'], true)) {
            return 'movie';
        }

        if (in_array($normalized, ['serial', 'seriale', 'series', 'tv'], true)) {
            return 'tv';
        }
    }

    $joined = normalize_key(implode(' ', array_map('strval', $cells)));

    if (preg_match('/\bfilm\b/', $joined) === 1) {
        return 'movie';
    }

    if (preg_match('/\bserial\b/', $joined) === 1) {
        return 'tv';
    }

    return null;
}

function media_label_from_type(?string $mediaType): string
{
    return match ($mediaType) {
        'movie' => 'Film',
        'tv' => 'Serial',
        default => '',
    };
}

function parse_date_iso(string $value): ?string
{
    if (trim($value) === '') {
        return null;
    }

    try {
        $date = new DateTimeImmutable($value);

        return $date->format('Y-m-d');
    } catch (Throwable $exception) {
        return null;
    }
}

function display_date(?string $isoDate): string
{
    if ($isoDate === null || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $isoDate)) {
        return '';
    }

    try {
        $date = new DateTimeImmutable($isoDate);

        return $date->format('d.m.Y');
    } catch (Throwable $exception) {
        return '';
    }
}

function recent_threshold_date(): string
{
    return (new DateTimeImmutable('today'))->modify('-' . (UPFLIX_RECENT_DAYS - 1) . ' days')->format('Y-m-d');
}

function article_is_recent(?string $date): bool
{
    if ($date === null || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        return true;
    }

    return $date >= recent_threshold_date();
}

function article_year(?string $articleDate): int
{
    if (is_string($articleDate) && preg_match('/^(\d{4})-\d{2}-\d{2}$/', $articleDate, $match) === 1) {
        return (int) $match[1];
    }

    return (int) date('Y');
}

function polish_month_number(string $monthName): ?int
{
    $monthName = normalize_key($monthName);

    return [
        'stycznia' => 1,
        'styczen' => 1,
        'lutego' => 2,
        'luty' => 2,
        'marca' => 3,
        'marzec' => 3,
        'kwietnia' => 4,
        'kwiecien' => 4,
        'maja' => 5,
        'maj' => 5,
        'czerwca' => 6,
        'czerwiec' => 6,
        'lipca' => 7,
        'lipiec' => 7,
        'sierpnia' => 8,
        'sierpien' => 8,
        'wrzesnia' => 9,
        'wrzesien' => 9,
        'pazdziernika' => 10,
        'pazdziernik' => 10,
        'listopada' => 11,
        'listopad' => 11,
        'grudnia' => 12,
        'grudzien' => 12,
    ][$monthName] ?? null;
}

function parse_polish_date_from_text(string $value, ?string $fallbackYearDate = null): ?string
{
    $value = clean_text($value);

    if ($value === '') {
        return null;
    }

    if (preg_match('/\b(20\d{2}|19\d{2})-(\d{2})-(\d{2})\b/u', $value, $match) === 1) {
        $year = (int) $match[1];
        $month = (int) $match[2];
        $day = (int) $match[3];

        if (checkdate($month, $day, $year)) {
            return sprintf('%04d-%02d-%02d', $year, $month, $day);
        }
    }

    if (preg_match('/\b(\d{1,2})[.\-\/](\d{1,2})[.\-\/](20\d{2}|19\d{2})\b/u', $value, $match) === 1) {
        $day = (int) $match[1];
        $month = (int) $match[2];
        $year = (int) $match[3];

        if (checkdate($month, $day, $year)) {
            return sprintf('%04d-%02d-%02d', $year, $month, $day);
        }
    }

    if (preg_match('/\b(\d{1,2})\s+([[:alpha:]ąćęłńóśźż]+)(?:\s+(20\d{2}|19\d{2}))?\b/iu', $value, $match) === 1) {
        $day = (int) $match[1];
        $month = polish_month_number($match[2]);
        $year = isset($match[3]) && $match[3] !== ''
            ? (int) $match[3]
            : article_year($fallbackYearDate);

        if ($month !== null && checkdate($month, $day, $year)) {
            return sprintf('%04d-%02d-%02d', $year, $month, $day);
        }
    }

    return null;
}

function cell_is_date_like(string $value, ?string $articleDate): bool
{
    return parse_polish_date_from_text($value, $articleDate) !== null;
}

function extract_article_date_from_html(string $html, ?string $fallback): ?string
{
    if (preg_match('/<meta\b[^>]*(?:property|name)=["\']article:published_time["\'][^>]*content=["\']([^"\']+)["\']/iu', $html, $match) === 1) {
        $date = parse_date_iso($match[1]);

        if ($date !== null) {
            return $date;
        }
    }

    if (preg_match('/<meta\b[^>]*content=["\']([^"\']+)["\'][^>]*(?:property|name)=["\']article:published_time["\']/iu', $html, $match) === 1) {
        $date = parse_date_iso($match[1]);

        if ($date !== null) {
            return $date;
        }
    }

    if (preg_match('/<time\b[^>]*datetime=["\']([^"\']+)["\']/iu', $html, $match) === 1) {
        $date = parse_date_iso($match[1]);

        if ($date !== null) {
            return $date;
        }
    }

    $top = clean_text(strip_tags(substr($html, 0, 8000)));
    $date = parse_polish_date_from_text($top, $fallback);

    return $date ?? $fallback;
}

function parse_rss_items(string $rssXml, int $limit = UPFLIX_RSS_SCAN_LIMIT): array
{
    if (!function_exists('simplexml_load_string')) {
        throw new RuntimeException('Na serwerze nie jest dostępne SimpleXML.');
    }

    $previousInternalErrors = libxml_use_internal_errors(true);
    $rss = simplexml_load_string($rssXml, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NONET);
    libxml_clear_errors();
    libxml_use_internal_errors($previousInternalErrors);

    if (!($rss instanceof SimpleXMLElement) || !isset($rss->channel->item)) {
        throw new RuntimeException('Nie udało się odczytać RSS Upflix.');
    }

    $items = [];

    foreach ($rss->channel->item as $item) {
        $title = clean_text((string) ($item->title ?? ''));
        $link = clean_text((string) ($item->link ?? ''));
        $pubDate = clean_text((string) ($item->pubDate ?? ''));

        if ($link === '') {
            continue;
        }

        $publishedAt = parse_date_iso($pubDate);

        $items[] = [
            'title' => $title,
            'url' => absolute_upflix_url($link),
            'publishedAt' => $publishedAt,
            'publishedRaw' => $pubDate,
        ];

        if (count($items) >= $limit) {
            break;
        }
    }

    if ($items === []) {
        throw new RuntimeException('RSS Upflix nie zawiera linków do artykułów.');
    }

    return $items;
}

function load_dom_document(string $html): DOMDocument
{
    if (!class_exists('DOMDocument')) {
        throw new RuntimeException('Na serwerze nie jest dostępny DOMDocument.');
    }

    $document = new DOMDocument('1.0', 'UTF-8');
    $previousInternalErrors = libxml_use_internal_errors(true);
    $document->loadHTML('<?xml encoding="utf-8" ?>' . $html);
    libxml_clear_errors();
    libxml_use_internal_errors($previousInternalErrors);

    return $document;
}

function section_type_from_heading(string $heading): ?string
{
    $normalized = normalize_key($heading);

    if ($normalized === '') {
        return null;
    }

    if (
        str_contains($normalized, 'dodane tytuly')
        || str_contains($normalized, 'dodano tytuly')
        || str_contains($normalized, 'dodane tytulow')
        || str_contains($normalized, 'dodano tytulow')
        || str_contains($normalized, 'nowo dodane')
        || str_contains($normalized, 'nowosci dodane')
        || str_contains($normalized, 'pelna lista tytulow')
        || (
            str_contains($normalized, 'dodane')
            && str_contains($normalized, 'tytul')
            && !str_contains($normalized, 'odcink')
            && !str_contains($normalized, 'usun')
        )
    ) {
        return 'added';
    }

    if (
        str_contains($normalized, 'ponownie dodane')
        || str_contains($normalized, 'ponownie dodano')
        || str_contains($normalized, 'powracaja')
        || str_contains($normalized, 'wrocily')
        || str_contains($normalized, 'wracaja')
    ) {
        return 'returned';
    }

    if (
        str_contains($normalized, 'nowe odcinki')
        || str_contains($normalized, 'dodane odcinki')
        || str_contains($normalized, 'uzupelnione tlumaczenia')
        || str_contains($normalized, 'nowe sezony')
    ) {
        return 'episodes';
    }

    if (
        str_contains($normalized, 'zaplanowano usuniecie')
        || str_contains($normalized, 'usunieto')
        || str_contains($normalized, 'usuniete tytuly')
        || str_contains($normalized, 'usuni te')
        || str_contains($normalized, 'koniec licencji')
        || str_contains($normalized, 'zobacz takze')
    ) {
        return 'stop';
    }

    return null;
}

function section_label(string $sectionType): string
{
    return match ($sectionType) {
        'added' => 'Dodane tytuły',
        'returned' => 'Ponownie dodane',
        default => 'Nowość',
    };
}

function first_anchor_href(DOMNode $node): string
{
    if ($node instanceof DOMElement && strtolower($node->tagName) === 'a') {
        return absolute_upflix_url($node->getAttribute('href'));
    }

    if (!($node instanceof DOMElement)) {
        return '';
    }

    $anchors = $node->getElementsByTagName('a');

    if ($anchors->length === 0) {
        return '';
    }

    return absolute_upflix_url($anchors->item(0)?->getAttribute('href') ?? '');
}

function extract_year_from_cells(array $cells): ?int
{
    for ($index = count($cells) - 1; $index >= 0; $index--) {
        if (preg_match('/\b(19|20)\d{2}\b/u', $cells[$index], $match) === 1) {
            return (int) $match[0];
        }
    }

    return null;
}

function extract_rating_from_cells(array $cells): string
{
    foreach ($cells as $cell) {
        $cell = trim($cell);

        if (preg_match('/^\d(?:[,.]\d)?$/u', $cell) === 1) {
            return str_replace(',', '.', $cell);
        }
    }

    return '';
}

function table_rows(DOMElement $table): array
{
    $rows = [];

    foreach ($table->getElementsByTagName('tr') as $row) {
        if (!($row instanceof DOMElement)) {
            continue;
        }

        $cells = [];

        foreach ($row->childNodes as $child) {
            if ($child instanceof DOMElement && in_array(strtolower($child->tagName), ['td', 'th'], true)) {
                $cells[] = [
                    'text' => clean_text($child->textContent ?? ''),
                    'node' => $child,
                ];
            }
        }

        if ($cells !== []) {
            $rows[] = $cells;
        }
    }

    return $rows;
}

function table_has_title_header(DOMElement $table): bool
{
    $rows = table_rows($table);

    if ($rows === []) {
        return false;
    }

    $firstRow = implode(' ', array_map(static fn (array $cell): string => $cell['text'], $rows[0]));
    $normalized = normalize_key($firstRow);

    return str_contains($normalized, 'tytul')
        || str_contains($normalized, 'ocena')
        || str_contains($normalized, 'rok')
        || str_contains($normalized, 'produkcja');
}

function parse_table_items(DOMElement $table, string $sectionType, string $articleUrl, ?string $articleDate): array
{
    $items = [];
    $rows = table_rows($table);

    foreach ($rows as $rowIndex => $rowCells) {
        $cells = array_map(static fn (array $cell): string => $cell['text'], $rowCells);

        if ($cells === []) {
            continue;
        }

        $rowText = normalize_key(implode(' ', $cells));

        if ($rowIndex === 0 && (str_contains($rowText, 'tytul') || str_contains($rowText, 'ocena'))) {
            continue;
        }

        $rawTitle = $cells[0] ?? '';
        $title = clean_title($rawTitle);

        if ($title === '' || normalize_key($title) === 'tytul') {
            continue;
        }

        $url = isset($rowCells[0]['node']) ? first_anchor_href($rowCells[0]['node']) : '';
        $rawOriginalTitle = '';
        $secondCell = $cells[1] ?? '';

        if (
            $secondCell !== ''
            && !cell_is_date_like($secondCell, $articleDate)
            && !preg_match('/^\d(?:[,.]\d)?$/u', $secondCell)
            && !preg_match('/^(19|20)\d{2}$/u', $secondCell)
        ) {
            $rawOriginalTitle = $secondCell;
        }

        $originalTitle = clean_title($rawOriginalTitle);

        if (normalize_key($originalTitle) === normalize_key($title)) {
            $originalTitle = '';
        }

        $year = extract_year_from_cells($cells);
        $rating = extract_rating_from_cells($cells);
        $note = extract_note_from_title($rawTitle);
        $mediaType = media_type_from_cells($cells, $url);

        $items[] = [
            'title' => $title,
            'originalTitle' => $originalTitle,
            'year' => $year,
            'rating' => $rating,
            'section' => section_label($sectionType),
            'sectionType' => $sectionType,
            'mediaType' => $mediaType,
            'mediaLabel' => media_label_from_type($mediaType),
            'note' => $note,
            'url' => $url !== '' ? $url : $articleUrl,
            'articleUrl' => $articleUrl,
            'addedDate' => $articleDate,
            'addedDateDisplay' => display_date($articleDate),
        ];
    }

    return $items;
}

function parse_table_html_items(string $tableHtml, string $sectionType, string $articleUrl, ?string $articleDate): array
{
    try {
        $document = load_dom_document('<div>' . $tableHtml . '</div>');
        $tables = $document->getElementsByTagName('table');

        if ($tables->length === 0) {
            return [];
        }

        $table = $tables->item(0);

        if (!($table instanceof DOMElement) || !table_has_title_header($table)) {
            return [];
        }

        return parse_table_items($table, $sectionType, $articleUrl, $articleDate);
    } catch (Throwable $exception) {
        return [];
    }
}

function news_content_roots(DOMDocument $document): array
{
    $xpath = new DOMXPath($document);
    $nodes = $xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " news-content ")]');

    $roots = [];

    if ($nodes instanceof DOMNodeList) {
        foreach ($nodes as $node) {
            if ($node instanceof DOMElement) {
                $roots[] = $node;
            }
        }
    }

    if ($roots !== []) {
        return $roots;
    }

    $body = $document->getElementsByTagName('body')->item(0);

    if ($body instanceof DOMElement) {
        return [$body];
    }

    return [$document->documentElement];
}

function element_can_be_section_heading(DOMElement $element): bool
{
    $tagName = strtolower($element->tagName);

    if (preg_match('/^h[1-6]$/', $tagName) === 1) {
        return true;
    }

    if (in_array($tagName, ['p', 'strong', 'b'], true)) {
        return true;
    }

    return false;
}

function collect_items_from_node(
    DOMNode $node,
    ?string &$currentSection,
    string $articleUrl,
    ?string $articleDate,
    array &$items
): void {
    foreach ($node->childNodes as $child) {
        if (!($child instanceof DOMElement)) {
            continue;
        }

        $tagName = strtolower($child->tagName);

        if ($tagName === 'table') {
            if (in_array($currentSection, ['added', 'returned'], true) && table_has_title_header($child)) {
                foreach (parse_table_items($child, $currentSection, $articleUrl, $articleDate) as $item) {
                    $items[] = $item;
                }
            }

            continue;
        }

        if (element_can_be_section_heading($child)) {
            $sectionType = section_type_from_heading($child->textContent ?? '');

            if ($sectionType !== null) {
                $currentSection = $sectionType;
            }
        }

        collect_items_from_node($child, $currentSection, $articleUrl, $articleDate, $items);
    }
}

function parse_article_items_dom(string $html, string $articleUrl, ?string $articleDate): array
{
    $document = load_dom_document($html);
    $items = [];

    foreach (news_content_roots($document) as $root) {
        $currentSection = null;
        collect_items_from_node($root, $currentSection, $articleUrl, $articleDate, $items);
    }

    return $items;
}

function parse_article_items_by_heading_segments(string $html, string $articleUrl, ?string $articleDate): array
{
    $items = [];
    $headingPattern = '/<(h[1-6]|strong|b)\b[^>]*>(.*?)<\/\1>/isu';

    if (preg_match_all($headingPattern, $html, $matches, PREG_OFFSET_CAPTURE) < 1) {
        return [];
    }

    $headings = [];

    foreach ($matches[0] as $index => $fullMatch) {
        $headingHtml = $fullMatch[0];
        $headingOffset = $fullMatch[1];
        $headingText = clean_text(strip_tags($headingHtml));
        $sectionType = section_type_from_heading($headingText);

        if ($sectionType === null) {
            continue;
        }

        $headings[] = [
            'html' => $headingHtml,
            'offset' => $headingOffset,
            'end' => $headingOffset + strlen($headingHtml),
            'sectionType' => $sectionType,
        ];
    }

    foreach ($headings as $index => $heading) {
        if (!in_array($heading['sectionType'], ['added', 'returned'], true)) {
            continue;
        }

        $segmentStart = $heading['end'];
        $segmentEnd = strlen($html);

        for ($nextIndex = $index + 1; $nextIndex < count($headings); $nextIndex++) {
            $segmentEnd = $headings[$nextIndex]['offset'];
            break;
        }

        $segment = substr($html, $segmentStart, $segmentEnd - $segmentStart);

        if (preg_match_all('/<table\b[^>]*>.*?<\/table>/isu', $segment, $tableMatches) < 1) {
            continue;
        }

        foreach ($tableMatches[0] as $tableHtml) {
            foreach (parse_table_html_items($tableHtml, $heading['sectionType'], $articleUrl, $articleDate) as $item) {
                $items[] = $item;
            }
        }
    }

    return $items;
}

function parse_article_items_by_specific_heading_regex(string $html, string $articleUrl, ?string $articleDate): array
{
    $items = [];

    $patterns = [
        'added' => [
            'dodane\s+tytu(?:ł|l)y',
            'dodano\s+tytu(?:ł|l)y',
            'nowo\s+dodane',
            'pełna\s+lista\s+tytu(?:ł|l)ów',
            'pelna\s+lista\s+tytulow',
        ],
        'returned' => [
            'ponownie\s+dodane',
            'ponownie\s+dodano',
            'powracaj(?:ą|a)',
            'wr(?:ó|o)ci(?:ł|l)y',
        ],
    ];

    foreach ($patterns as $sectionType => $headingPatterns) {
        $headingAlternation = implode('|', $headingPatterns);
        $pattern = '/<(h[1-6]|strong|b)\b[^>]*>\s*(?:' . $headingAlternation . ')\s*:?\s*<\/\1>(.*?)(?=<(?:h[1-6]|strong|b)\b[^>]*>|<\/div>\s*<\/div>|$)/isu';

        if (preg_match_all($pattern, $html, $matches) < 1) {
            continue;
        }

        foreach ($matches[2] as $segment) {
            if (preg_match_all('/<table\b[^>]*>.*?<\/table>/isu', $segment, $tableMatches) < 1) {
                continue;
            }

            foreach ($tableMatches[0] as $tableHtml) {
                foreach (parse_table_html_items($tableHtml, $sectionType, $articleUrl, $articleDate) as $item) {
                    $items[] = $item;
                }
            }
        }
    }

    return $items;
}

function dedupe_items(array $items): array
{
    $seen = [];
    $unique = [];

    foreach ($items as $item) {
        $key = implode('|', [
            normalize_key((string) ($item['title'] ?? '')),
            normalize_key((string) ($item['originalTitle'] ?? '')),
            (string) ($item['year'] ?? ''),
            (string) ($item['sectionType'] ?? ''),
            (string) ($item['addedDate'] ?? ''),
        ]);

        if ($key === '||||' || isset($seen[$key])) {
            continue;
        }

        $seen[$key] = true;
        $unique[] = $item;
    }

    return $unique;
}

function parse_article_items(string $html, string $articleUrl, ?string $articleDate): array
{
    $items = [];

    try {
        $items = array_merge($items, parse_article_items_dom($html, $articleUrl, $articleDate));
    } catch (Throwable $exception) {
    }

    $items = array_merge($items, parse_article_items_by_heading_segments($html, $articleUrl, $articleDate));
    $items = array_merge($items, parse_article_items_by_specific_heading_regex($html, $articleUrl, $articleDate));

    return dedupe_items($items);
}

function sort_items_latest_first(array $items): array
{
    usort($items, static function (array $left, array $right): int {
        $leftDate = (string) ($left['addedDate'] ?? '');
        $rightDate = (string) ($right['addedDate'] ?? '');

        if ($leftDate !== $rightDate) {
            return strcmp($rightDate, $leftDate);
        }

        $leftSection = (string) ($left['sectionType'] ?? '');
        $rightSection = (string) ($right['sectionType'] ?? '');

        if ($leftSection !== $rightSection) {
            $order = ['added' => 0, 'returned' => 1];

            return ($order[$leftSection] ?? 99) <=> ($order[$rightSection] ?? 99);
        }

        return strcmp(
            normalize_key((string) ($left['title'] ?? '')),
            normalize_key((string) ($right['title'] ?? ''))
        );
    });

    return $items;
}

function build_payload(string $platform, int $limit): array
{
    $rssUrl = UPFLIX_RSS_URLS[$platform] ?? null;

    if ($rssUrl === null) {
        throw new RuntimeException('Nieobsługiwana platforma.');
    }

    $rssXml = http_get($rssUrl);
    $articles = parse_rss_items($rssXml, UPFLIX_RSS_SCAN_LIMIT);
    $scannedArticles = [];
    $allItems = [];
    $lastError = null;

    foreach ($articles as $article) {
        if (!article_is_recent($article['publishedAt'])) {
            break;
        }

        try {
            $articleHtml = http_get($article['url']);
            $articleDate = extract_article_date_from_html($articleHtml, $article['publishedAt']);
            $article['publishedAt'] = $articleDate;

            if (!article_is_recent($articleDate)) {
                break;
            }

            $items = parse_article_items($articleHtml, $article['url'], $articleDate);

            foreach ($items as &$item) {
                $item['sourceArticleTitle'] = $article['title'];
                $item['sourceArticleUrl'] = $article['url'];
            }
            unset($item);

            $scannedArticles[] = [
                'title' => $article['title'],
                'url' => $article['url'],
                'publishedAt' => $article['publishedAt'],
                'itemsFound' => count($items),
            ];

            $allItems = array_merge($allItems, $items);
        } catch (Throwable $exception) {
            $lastError = $exception->getMessage();

            $scannedArticles[] = [
                'title' => $article['title'],
                'url' => $article['url'],
                'publishedAt' => $article['publishedAt'],
                'itemsFound' => 0,
                'error' => $lastError,
            ];

            continue;
        }
    }

    $allItems = sort_items_latest_first(dedupe_items($allItems));
    $totalCount = count($allItems);

    if ($limit > 0) {
        $allItems = array_slice($allItems, 0, $limit);
    }

    if ($totalCount === 0) {
        $message = 'Brak nowo dodanych lub ponownie dodanych pozycji w ostatnich ' . UPFLIX_RECENT_DAYS . ' dniach.';

        if ($lastError !== null) {
            $message .= ' Ostatni błąd: ' . $lastError;
        }

        throw new RuntimeException($message);
    }

    return [
        'platform' => $platform,
        'source' => 'upflix',
        'rangeDays' => UPFLIX_RECENT_DAYS,
        'scannedArticles' => $scannedArticles,
        'scannedCount' => count($scannedArticles),
        'items' => $allItems,
        'count' => count($allItems),
        'totalCount' => $totalCount,
        'generatedAt' => (new DateTimeImmutable())->format(DateTimeInterface::ATOM),
    ];
}

function build_payload_with_scrapling(string $platform, int $limit): ?array
{
    $payload = scraplingBridgeRun('upflix', [
        'platform' => $platform,
        'limit' => $limit,
    ], 75);

    if (!is_array($payload)) {
        return null;
    }

    if (($payload['source'] ?? '') !== 'upflix') {
        return null;
    }

    if ((int) ($payload['totalCount'] ?? 0) <= 0) {
        return null;
    }

    $payload['engine'] = 'scrapling';

    return $payload;
}

function payload_has_added_items(array $payload): bool
{
    foreach (($payload['items'] ?? []) as $item) {
        if (($item['sectionType'] ?? '') === 'added') {
            return true;
        }
    }

    return false;
}

function payload_section_types_seen(array $payload): array
{
    $seen = [];
    $collect = static function ($values) use (&$seen): void {
        if (!is_array($values)) {
            return;
        }

        foreach ($values as $value) {
            if (is_string($value) && $value !== '') {
                $seen[$value] = true;
            }
        }
    };

    $collect($payload['sectionTypesSeen'] ?? null);

    foreach (($payload['scannedArticles'] ?? []) as $article) {
        if (is_array($article)) {
            $collect($article['sectionTypesSeen'] ?? null);
        }
    }

    return array_keys($seen);
}

function should_fallback_after_scrapling(?array $payload): bool
{
    if (!is_array($payload)) {
        return true;
    }

    if (payload_has_added_items($payload)) {
        return false;
    }

    $sectionTypesSeen = payload_section_types_seen($payload);

    if ($sectionTypesSeen === []) {
        return true;
    }

    return in_array('added', $sectionTypesSeen, true);
}

function merge_upflix_payloads(?array $scraplingPayload, ?array $phpPayload, int $limit): array
{
    if (!is_array($scraplingPayload) && !is_array($phpPayload)) {
        throw new RuntimeException('Nie udało się pobrać danych z Upflix.');
    }

    if (!is_array($scraplingPayload)) {
        $phpPayload['engine'] = 'php';

        return $phpPayload;
    }

    if (!is_array($phpPayload)) {
        return $scraplingPayload;
    }

    $mergedItems = sort_items_latest_first(dedupe_items(array_merge(
        is_array($scraplingPayload['items'] ?? null) ? $scraplingPayload['items'] : [],
        is_array($phpPayload['items'] ?? null) ? $phpPayload['items'] : []
    )));

    $totalCount = count($mergedItems);

    if ($limit > 0) {
        $mergedItems = array_slice($mergedItems, 0, $limit);
    }

    return [
        ...$scraplingPayload,
        'items' => $mergedItems,
        'count' => count($mergedItems),
        'totalCount' => $totalCount,
        'engine' => 'scrapling+php',
        'phpCount' => (int) ($phpPayload['count'] ?? 0),
        'scraplingCount' => (int) ($scraplingPayload['count'] ?? 0),
        'phpHadAddedItems' => payload_has_added_items($phpPayload),
        'scraplingHadAddedItems' => payload_has_added_items($scraplingPayload),
    ];
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
    json_response(405, ['error' => 'Dozwolona jest tylko metoda GET.']);
}

$platform = request_value('platform');
$limit = request_limit();

if (!in_array($platform, UPFLIX_ALLOWED_PLATFORMS, true)) {
    json_response(400, ['error' => 'Nieprawidłowa platforma. Użyj platform=netflix albo platform=hbomax.']);
}

try {
    $engine = request_value('engine');

    if ($engine === 'php') {
        $payload = build_payload($platform, $limit);
        $payload['engine'] = 'php';
        $payload['cache'] = 'fresh';

        json_response(200, $payload);
    }

    $scraplingPayload = build_payload_with_scrapling($platform, $limit);
    $phpPayload = null;
    $phpError = null;

    /*
     * Scrapling bywa szybszy, ale potrafi zwrócić tylko część artykułu.
     * Fallback uruchamiamy wtedy, gdy widział sekcję "Dodane tytuły",
     * ale nie zwrócił z niej pozycji. Same "Ponownie dodane" są poprawnym wynikiem.
     */
    if (should_fallback_after_scrapling($scraplingPayload)) {
        try {
            $phpPayload = build_payload($platform, $limit);
            $phpPayload['engine'] = 'php';
        } catch (Throwable $exception) {
            $phpError = $exception->getMessage();
        }
    }

    $payload = merge_upflix_payloads($scraplingPayload, $phpPayload, $limit);

    if ($phpError !== null) {
        $payload['phpFallbackError'] = $phpError;
    }

    $payload['cache'] = 'fresh';

    json_response(200, $payload);
} catch (Throwable $exception) {
    json_response(502, ['error' => $exception->getMessage()]);
}
