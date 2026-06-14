<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . DIRECTORY_SEPARATOR . 'scrapling-bridge.php';

const FILMWEB_API_BASE = 'https://www.filmweb.pl/api/v1';
const FILMWEB_DEFAULT_LOCALE = 'pl-PL';
const FILMWEB_PROVIDER_LOGO_BASE = 'https://fwcdn.pl/vodp';
const FILMWEB_PAGE_BASE = 'https://www.filmweb.pl';
const FILMWEB_ALLOWED_MEDIA_TYPES = ['film', 'serial'];

function jsonResponse(int $statusCode, array $payload): never
{
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function requestValue(string $key): string
{
    $value = $_GET[$key] ?? '';

    return is_string($value) ? trim($value) : '';
}

function requestYear(string $value): ?int
{
    if ($value === '' || !preg_match('/^\d{4}$/', $value)) {
        return null;
    }

    return (int) $value;
}

function filmwebHeaders(array $extraHeaders = []): array
{
    return array_merge([
        'Accept: application/json',
        'x-locale: ' . FILMWEB_DEFAULT_LOCALE,
    ], $extraHeaders);
}

function filmwebRequest(string $path, array $query = [], array $extraHeaders = []): array
{
    $url = FILMWEB_API_BASE . $path;
    $queryString = http_build_query($query);

    if ($queryString !== '') {
        $url .= '?' . $queryString;
    }

    $headers = filmwebHeaders($extraHeaders);

    if (function_exists('curl_init')) {
        $curlHandle = curl_init($url);

        curl_setopt_array($curlHandle, [
            CURLOPT_HTTPGET => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_HTTPHEADER => $headers,
        ]);

        $body = curl_exec($curlHandle);

        if ($body === false) {
            $errorMessage = curl_error($curlHandle);
            curl_close($curlHandle);
            throw new RuntimeException('Nie udało się połączyć z Filmweb: ' . $errorMessage);
        }

        $statusCode = (int) curl_getinfo($curlHandle, CURLINFO_RESPONSE_CODE);
        curl_close($curlHandle);

        return [$statusCode, (string) $body];
    }

    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 20,
            'ignore_errors' => true,
            'header' => implode("\r\n", $headers),
        ],
    ]);

    $body = @file_get_contents($url, false, $context);

    if ($body === false) {
        throw new RuntimeException('Nie udało się połączyć z Filmweb z poziomu serwera.');
    }

    $statusCode = 200;

    if (isset($http_response_header) && is_array($http_response_header)) {
        foreach ($http_response_header as $headerLine) {
            if (preg_match('#^HTTP/\S+\s+(\d{3})#', $headerLine, $matches)) {
                $statusCode = (int) $matches[1];
                break;
            }
        }
    }

    return [$statusCode, (string) $body];
}

function filmwebJson(string $path, array $query = []): array
{
    [$statusCode, $body] = filmwebRequest($path, $query);

    if ($statusCode < 200 || $statusCode >= 300) {
        throw new RuntimeException('Filmweb zwrócił błąd HTTP ' . $statusCode . '.');
    }

    if ($body === '') {
        return [];
    }

    $payload = json_decode($body, true);

    if (!is_array($payload)) {
        throw new RuntimeException('Filmweb zwrócił nieprawidłowy JSON.');
    }

    return $payload;
}


function requestIntValue(string $key, int $default, int $min, int $max): int
{
    $value = requestValue($key);

    if ($value === '' || !preg_match('/^-?\d+$/', $value)) {
        return $default;
    }

    $number = (int) $value;

    return max($min, min($max, $number));
}

function filmwebPremiereKind(): string
{
    $kind = requestValue('kind');

    return match ($kind) {
        'tv', 'serial', 'serials' => 'tv',
        default => 'movie',
    };
}

function filmwebPremiereTypeForKind(string $kind): string
{
    return $kind === 'tv' ? 'serial' : 'film';
}

function filmwebPremiereUrlForMonth(string $kind, int $year, int $month): string
{
    $path = $kind === 'tv' ? '/serials/premiere' : '/premiere';

    return FILMWEB_PAGE_BASE . $path . '/' . $year . '/' . $month;
}

function filmwebPageHeaders(): array
{
    return [
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language: pl-PL,pl;q=0.9,en;q=0.7',
        'Cache-Control: no-cache',
        'Pragma: no-cache',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
    ];
}

function filmwebPageRequest(string $url): string
{
    $headers = filmwebPageHeaders();

    if (function_exists('curl_init')) {
        $curlHandle = curl_init($url);

        curl_setopt_array($curlHandle, [
            CURLOPT_HTTPGET => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 4,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_HTTPHEADER => $headers,
        ]);

        $body = curl_exec($curlHandle);

        if ($body === false) {
            $errorMessage = curl_error($curlHandle);
            curl_close($curlHandle);
            throw new RuntimeException('Nie udało się pobrać strony premier Filmweb: ' . $errorMessage);
        }

        $statusCode = (int) curl_getinfo($curlHandle, CURLINFO_RESPONSE_CODE);
        curl_close($curlHandle);

        if ($statusCode < 200 || $statusCode >= 300) {
            throw new RuntimeException('Filmweb zwrócił błąd HTTP ' . $statusCode . ' dla strony premier.');
        }

        return (string) $body;
    }

    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 20,
            'ignore_errors' => true,
            'header' => implode("\r\n", $headers),
        ],
    ]);

    $body = @file_get_contents($url, false, $context);

    if ($body === false) {
        throw new RuntimeException('Nie udało się pobrać strony premier Filmweb z poziomu serwera.');
    }

    return (string) $body;
}

function cleanHtmlText(string $value): string
{
    $value = html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $value = preg_replace('/\s+/u', ' ', $value) ?? $value;

    return trim($value);
}

function filmwebAbsolutePageUrl(string $url): string
{
    $url = trim(html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8'));

    if ($url === '') {
        return '';
    }

    if (preg_match('~^https?://~i', $url) === 1) {
        return $url;
    }

    if (str_starts_with($url, '//')) {
        return 'https:' . $url;
    }

    if (str_starts_with($url, '/')) {
        return FILMWEB_PAGE_BASE . $url;
    }

    return FILMWEB_PAGE_BASE . '/' . ltrim($url, '/');
}

function extractIsoDateCandidatesFromText(string $text, int $targetYear, int $targetMonth): array
{
    $dates = [];
    $targetMonthPrefix = sprintf('%04d-%02d-', $targetYear, $targetMonth);

    if (preg_match_all('/\b(20\d{2})-(\d{2})-(\d{2})\b/', $text, $matches, PREG_SET_ORDER) > 0) {
        foreach ($matches as $match) {
            $iso = $match[1] . '-' . $match[2] . '-' . $match[3];

            if (str_starts_with($iso, $targetMonthPrefix)) {
                $dates[] = $iso;
            }
        }
    }

    if (preg_match_all('/\b(20\d{2})[\/.](\d{1,2})[\/.](\d{1,2})\b/', $text, $matches, PREG_SET_ORDER) > 0) {
        foreach ($matches as $match) {
            $month = (int) $match[2];
            $day = (int) $match[3];

            if ((int) $match[1] === $targetYear && $month === $targetMonth && checkdate($month, $day, $targetYear)) {
                $dates[] = sprintf('%04d-%02d-%02d', $targetYear, $month, $day);
            }
        }
    }

    return array_values(array_unique($dates));
}

function collectDatesFromValue(mixed $value, string $path, array &$dates): void
{
    if (is_string($value)) {
        if (preg_match('/\b(20\d{2})-(\d{2})-(\d{2})\b/', $value, $match) === 1) {
            $year = (int) $match[1];
            $month = (int) $match[2];
            $day = (int) $match[3];

            if (checkdate($month, $day, $year)) {
                $dates[] = [
                    'iso' => sprintf('%04d-%02d-%02d', $year, $month, $day),
                    'path' => $path,
                ];
            }
        }

        return;
    }

    if (!is_array($value)) {
        return;
    }

    $year = isset($value['year']) && is_numeric($value['year']) ? (int) $value['year'] : null;
    $month = isset($value['month']) && is_numeric($value['month']) ? (int) $value['month'] : null;
    $day = isset($value['day']) && is_numeric($value['day']) ? (int) $value['day'] : null;

    if ($year !== null && $month !== null && $day !== null && checkdate($month, $day, $year)) {
        $dates[] = [
            'iso' => sprintf('%04d-%02d-%02d', $year, $month, $day),
            'path' => $path,
        ];
    }

    foreach ($value as $key => $child) {
        collectDatesFromValue($child, $path . '.' . (string) $key, $dates);
    }
}

function titleInfoPremiereIso(array $titleInfo, int $targetYear, int $targetMonth): ?string
{
    $dates = [];
    collectDatesFromValue($titleInfo, 'root', $dates);

    $targetPrefix = sprintf('%04d-%02d-', $targetYear, $targetMonth);
    $candidates = [];

    foreach ($dates as $date) {
        $iso = (string) ($date['iso'] ?? '');

        if (!str_starts_with($iso, $targetPrefix)) {
            continue;
        }

        $path = strtolower((string) ($date['path'] ?? ''));
        $score = 0;

        if (str_contains($path, 'premiere')) {
            $score += 80;
        }

        if (str_contains($path, 'pol') || str_contains($path, 'pl') || str_contains($path, 'country')) {
            $score += 60;
        }

        if (str_contains($path, 'date')) {
            $score += 20;
        }

        if (str_contains($path, 'world')) {
            $score -= 15;
        }

        if (str_contains($path, 'vod')) {
            $score -= 80;
        }

        $candidates[] = [
            'iso' => $iso,
            'score' => $score,
        ];
    }

    if ($candidates === []) {
        return null;
    }

    usort($candidates, static function (array $left, array $right): int {
        if ($left['score'] !== $right['score']) {
            return $right['score'] <=> $left['score'];
        }

        return strcmp((string) $left['iso'], (string) $right['iso']);
    });

    return (string) $candidates[0]['iso'];
}

function extractFilmwebPremiereLinks(string $html, string $kind, int $targetYear, int $targetMonth): array
{
    $expectedType = filmwebPremiereTypeForKind($kind);
    $items = [];

    if (preg_match_all('/<a\b[^>]*href=("|\')([^"\']+)\1[^>]*>(.*?)<\/a>/isu', $html, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE) <= 0) {
        return [];
    }

    foreach ($matches as $match) {
        $href = html_entity_decode((string) $match[2][0], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $path = parse_url($href, PHP_URL_PATH);

        if (!is_string($path) || $path === '') {
            $path = $href;
        }

        if (preg_match('#^/(film|serial)/(.+)-(\d{4})-(\d+)(?:/)?$#u', $path, $urlMatch) !== 1) {
            continue;
        }

        $type = $urlMatch[1];

        if ($type !== $expectedType) {
            continue;
        }

        $id = (int) $urlMatch[4];

        if ($id <= 0) {
            continue;
        }

        $label = cleanHtmlText((string) $match[3][0]);
        $offset = (int) $match[0][1];
        $context = substr($html, max(0, $offset - 1800), 3600);
        $contextDates = is_string($context)
            ? extractIsoDateCandidatesFromText($context, $targetYear, $targetMonth)
            : [];

        if (!isset($items[$id])) {
            $items[$id] = [
                'id' => $id,
                'url' => filmwebAbsolutePageUrl($href),
                'title' => $label,
                'fallbackDateIso' => $contextDates[0] ?? null,
            ];

            continue;
        }

        if ((string) $items[$id]['title'] === '' && $label !== '') {
            $items[$id]['title'] = $label;
        }

        if (($items[$id]['fallbackDateIso'] ?? null) === null && $contextDates !== []) {
            $items[$id]['fallbackDateIso'] = $contextDates[0];
        }
    }

    return array_values($items);
}

function titleInfoYear(array $titleInfo, ?int $fallbackYear = null): ?int
{
    if (isset($titleInfo['year']) && is_numeric($titleInfo['year'])) {
        return (int) $titleInfo['year'];
    }

    return $fallbackYear;
}

function formatPolishIsoDate(string $iso): string
{
    $date = DateTimeImmutable::createFromFormat('Y-m-d', $iso);

    return $date instanceof DateTimeImmutable ? $date->format('d.m.Y') : $iso;
}

function normalizePremiereItem(array $linkItem, array $titleInfo, string $kind, int $targetYear, int $targetMonth): ?array
{
    $premiereDate = titleInfoPremiereIso($titleInfo, $targetYear, $targetMonth);

    if ($premiereDate === null && isset($linkItem['fallbackDateIso']) && is_string($linkItem['fallbackDateIso'])) {
        $premiereDate = $linkItem['fallbackDateIso'];
    }

    if ($premiereDate === null) {
        return null;
    }

    $title = trim((string) ($titleInfo['title'] ?? $linkItem['title'] ?? ''));
    $originalTitle = trim((string) ($titleInfo['originalTitle'] ?? ''));

    if ($title === '') {
        $title = $originalTitle !== '' ? $originalTitle : 'Bez tytułu';
    }

    $filmwebType = filmwebPremiereTypeForKind($kind);
    $year = titleInfoYear($titleInfo, null);
    $url = (string) ($linkItem['url'] ?? '');

    if ($url === '' && $year !== null) {
        $url = filmwebEntityUrl($filmwebType, $title, $year, (int) $linkItem['id']);
    }

    return [
        'id' => (int) $linkItem['id'],
        'mediaType' => $kind === 'tv' ? 'tv' : 'movie',
        'filmwebType' => $filmwebType,
        'title' => $title,
        'originalTitle' => $originalTitle,
        'englishTitle' => '',
        'year' => $year,
        'premiereDate' => $premiereDate,
        'releaseLabel' => formatPolishIsoDate($premiereDate),
        'genres' => [],
        'overview' => '',
        'poster' => null,
        'filmwebUrl' => $url,
    ];
}

function premiereMonthPayload(string $kind, int $year, int $month): array
{
    if ($month < 1 || $month > 12) {
        jsonResponse(400, ['error' => 'Nieprawidłowy miesiąc premier.']);
    }

    $url = filmwebPremiereUrlForMonth($kind, $year, $month);
    $html = filmwebPageRequest($url);
    $links = extractFilmwebPremiereLinks($html, $kind, $year, $month);
    $items = [];

    foreach ($links as $linkItem) {
        try {
            $titleInfo = fetchTitleInfo((int) $linkItem['id']);
            $item = normalizePremiereItem($linkItem, $titleInfo, $kind, $year, $month);

            if ($item !== null) {
                $items[] = $item;
            }
        } catch (Throwable $exception) {
            continue;
        }
    }

    usort($items, static function (array $left, array $right): int {
        $dateCompare = strcmp((string) ($left['premiereDate'] ?? ''), (string) ($right['premiereDate'] ?? ''));

        if ($dateCompare !== 0) {
            return $dateCompare;
        }

        return strcmp((string) ($left['title'] ?? ''), (string) ($right['title'] ?? ''));
    });

    return [
        'source' => 'filmweb',
        'url' => $url,
        'kind' => $kind,
        'year' => $year,
        'month' => $month,
        'monthKey' => sprintf('%04d-%02d', $year, $month),
        'items' => $items,
    ];
}

function premiereMonthPayloadWithScrapling(string $kind, int $year, int $month): ?array
{
    $payload = scraplingBridgeRun('filmweb-premieres', [
        'kind' => $kind,
        'year' => $year,
        'month' => $month,
    ], 45);

    if (!is_array($payload)) {
        return null;
    }

    if (($payload['source'] ?? '') !== 'filmweb') {
        return null;
    }

    if (!is_array($payload['items'] ?? null) || count($payload['items']) === 0) {
        return null;
    }

    $datedItems = array_values(array_filter(
        $payload['items'],
        static fn (array $item): bool => is_string($item['premiereDate'] ?? null)
            && preg_match('/^\d{4}-\d{2}-\d{2}$/', $item['premiereDate']) === 1
    ));

    if ($datedItems === []) {
        return null;
    }

    $payload['items'] = $datedItems;
    $payload['engine'] = 'scrapling';

    return $payload;
}

function filmwebCleanText(string $value): string
{
    $value = html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $value = preg_replace('/\s+/u', ' ', $value) ?? $value;

    return trim($value);
}

function filmwebAbsoluteUrl(string $url): string
{
    $url = trim(html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8'));

    if ($url === '') {
        return '';
    }

    if (preg_match('~^https?://~i', $url) === 1) {
        return $url;
    }

    if (str_starts_with($url, '//')) {
        return 'https:' . $url;
    }

    if (str_starts_with($url, '/')) {
        return 'https://www.filmweb.pl' . $url;
    }

    return 'https://www.filmweb.pl/' . ltrim($url, '/');
}

function filmwebMonthName(int $month): string
{
    return [
        1 => 'styczeń',
        2 => 'luty',
        3 => 'marzec',
        4 => 'kwiecień',
        5 => 'maj',
        6 => 'czerwiec',
        7 => 'lipiec',
        8 => 'sierpień',
        9 => 'wrzesień',
        10 => 'październik',
        11 => 'listopad',
        12 => 'grudzień',
    ][$month] ?? '';
}

function filmwebPolishMonthNumber(string $monthName): ?int
{
    $normalized = mb_strtolower(trim($monthName), 'UTF-8');

    return [
        'stycznia' => 1,
        'styczen' => 1,
        'styczeń' => 1,
        'lutego' => 2,
        'luty' => 2,
        'marca' => 3,
        'marzec' => 3,
        'kwietnia' => 4,
        'kwiecien' => 4,
        'kwiecień' => 4,
        'maja' => 5,
        'maj' => 5,
        'czerwca' => 6,
        'czerwiec' => 6,
        'lipca' => 7,
        'lipiec' => 7,
        'sierpnia' => 8,
        'sierpien' => 8,
        'sierpień' => 8,
        'września' => 9,
        'wrzesnia' => 9,
        'wrzesien' => 9,
        'wrzesień' => 9,
        'października' => 10,
        'pazdziernika' => 10,
        'pazdziernik' => 10,
        'październik' => 10,
        'listopada' => 11,
        'listopad' => 11,
        'grudnia' => 12,
        'grudzien' => 12,
        'grudzień' => 12,
    ][$normalized] ?? null;
}

function filmwebPremiereDateFromText(string $text, int $year, int $month): ?string
{
    $text = filmwebCleanText($text);

    if ($text === '') {
        return null;
    }

    if (preg_match('/\b(\d{1,2})\s+(stycznia|lutego|marca|kwietnia|maja|czerwca|lipca|sierpnia|września|wrzesnia|października|pazdziernika|listopada|grudnia)\b/iu', $text, $match) !== 1) {
        return null;
    }

    $day = (int) $match[1];
    $foundMonth = filmwebPolishMonthNumber($match[2]);

    if ($foundMonth !== $month || !checkdate($month, $day, $year)) {
        return null;
    }

    return sprintf('%04d-%02d-%02d', $year, $month, $day);
}

function filmwebPremierePageUrl(string $kind, int $year, int $month): string
{
    if ($kind === 'tv') {
        return sprintf('https://www.filmweb.pl/serials/premiere/%d/%d', $year, $month);
    }

    return sprintf('https://www.filmweb.pl/premiere/%d/%d', $year, $month);
}

function filmwebTitleTypeFromKind(string $kind): string
{
    return $kind === 'tv' ? 'serial' : 'film';
}

function filmwebPremierePosterNearNode(DOMElement $link): string
{
    $node = $link;

    for ($depth = 0; $depth < 5 && $node instanceof DOMNode; $depth++) {
        if ($node instanceof DOMElement) {
            $images = $node->getElementsByTagName('img');

            foreach ($images as $image) {
                if (!$image instanceof DOMElement) {
                    continue;
                }

                $src = trim($image->getAttribute('src'));

                if ($src === '') {
                    $src = trim($image->getAttribute('data-src'));
                }

                if ($src === '') {
                    continue;
                }

                return filmwebAbsoluteUrl($src);
            }
        }

        $node = $node->parentNode;
    }

    return '';
}

function filmwebTraversePremiereDom(
    DOMNode $node,
    array &$items,
    array &$seen,
    ?string &$currentDate,
    string $kind,
    int $year,
    int $month
): void {
    if ($node instanceof DOMText) {
        $date = filmwebPremiereDateFromText($node->nodeValue, $year, $month);

        if ($date !== null) {
            $currentDate = $date;
        }

        return;
    }

    if ($node instanceof DOMElement) {
        $tagName = strtolower($node->tagName);

        if ($tagName === 'script' || $tagName === 'style' || $tagName === 'noscript') {
            return;
        }

        if ($tagName === 'a') {
            $href = trim($node->getAttribute('href'));
            $expectedType = filmwebTitleTypeFromKind($kind);

            if (preg_match('~^/' . preg_quote($expectedType, '~') . '/[^"\']+-(\d{4})-(\d+)~u', $href, $match) === 1) {
                $title = filmwebCleanText($node->textContent);
                $url = filmwebAbsoluteUrl($href);

                if ($title !== '' && $url !== '' && !isset($seen[$url])) {
                    $seen[$url] = true;

                    $items[] = [
                        'id' => (int) $match[2],
                        'mediaType' => $kind,
                        'title' => $title,
                        'originalTitle' => '',
                        'year' => (int) $match[1],
                        'premiereDate' => $currentDate,
                        'releaseLabel' => $currentDate !== null ? date('d.m.Y', strtotime($currentDate)) : '',
                        'poster' => filmwebPremierePosterNearNode($node),
                        'filmwebUrl' => $url,
                        'url' => $url,
                    ];
                }
            }
        }
    }

    foreach ($node->childNodes as $childNode) {
        filmwebTraversePremiereDom($childNode, $items, $seen, $currentDate, $kind, $year, $month);
    }
}

function filmwebParsePremieresHtml(string $html, string $kind, int $year, int $month): array
{
    if (!class_exists('DOMDocument')) {
        throw new RuntimeException('Brak rozszerzenia DOMDocument w PHP.');
    }

    $dom = new DOMDocument('1.0', 'UTF-8');

    $previous = libxml_use_internal_errors(true);
    $loaded = $dom->loadHTML('<?xml encoding="UTF-8">' . $html);
    libxml_clear_errors();
    libxml_use_internal_errors($previous);

    if (!$loaded) {
        throw new RuntimeException('Nie udało się sparsować HTML Filmweb.');
    }

    $items = [];
    $seen = [];
    $currentDate = null;

    filmwebTraversePremiereDom($dom, $items, $seen, $currentDate, $kind, $year, $month);

    usort($items, static function (array $left, array $right): int {
        $leftDate = (string) ($left['premiereDate'] ?? '9999-12-31');
        $rightDate = (string) ($right['premiereDate'] ?? '9999-12-31');

        if ($leftDate !== $rightDate) {
            return strcmp($leftDate, $rightDate);
        }

        return strcmp((string) ($left['title'] ?? ''), (string) ($right['title'] ?? ''));
    });

    return $items;
}

function normalizeComparableText(string $value): string
{
    $value = trim($value);

    if ($value === '') {
        return '';
    }

    if (function_exists('iconv')) {
        $transliterated = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);

        if (is_string($transliterated) && $transliterated !== '') {
            $value = $transliterated;
        }
    }

    $value = strtolower($value);
    $value = preg_replace('/[^a-z0-9]+/', ' ', $value) ?? '';

    return trim($value);
}

function compactComparableText(string $value): string
{
    return str_replace(' ', '', normalizeComparableText($value));
}

function titleMatchScore(string $candidate, string $expected): int
{
    $candidateCompact = compactComparableText($candidate);
    $expectedCompact = compactComparableText($expected);

    if ($candidateCompact === '' || $expectedCompact === '') {
        return 0;
    }

    if ($candidateCompact === $expectedCompact) {
        return 600;
    }

    $candidateLength = strlen($candidateCompact);
    $expectedLength = strlen($expectedCompact);
    $shortLength = min($candidateLength, $expectedLength);
    $longLength = max($candidateLength, $expectedLength);

    if (
        (str_contains($candidateCompact, $expectedCompact) || str_contains($expectedCompact, $candidateCompact))
        && $shortLength >= 4
        && ($shortLength / max(1, $longLength)) >= 0.70
    ) {
        return 250;
    }

    if (abs($candidateLength - $expectedLength) <= 3) {
        $distance = levenshtein($candidateCompact, $expectedCompact);

        if ($distance <= 1) {
            return 220;
        }

        if ($distance <= 2) {
            return 160;
        }

        if ($distance <= 3) {
            return 90;
        }
    }

    similar_text($candidateCompact, $expectedCompact, $percent);

    if ($percent >= 90.0) {
        return 120;
    }

    if ($percent >= 80.0) {
        return 80;
    }

    return 0;
}

function filmwebSlug(string $value): string
{
    $value = preg_replace('/[?!;\/#\s]+/u', ' ', trim($value)) ?? trim($value);
    $slug = rawurlencode($value);
    $slug = str_replace('%20', '+', $slug);

    return preg_replace('/\++/', '+', $slug) ?? $slug;
}

function filmwebSearchUrl(string $title, ?int $year): string
{
    $query = trim(implode(' ', array_filter([$title, $year ? (string) $year : ''])));

    return 'https://www.filmweb.pl/search#/all?query=' . rawurlencode($query);
}

function filmwebEntityUrl(string $type, string $title, int $year, int $id): string
{
    return sprintf(
        'https://www.filmweb.pl/%s/%s-%d-%d',
        $type,
        filmwebSlug($title),
        $year,
        $id
    );
}

function fetchTitleInfo(int $id): array
{
    return filmwebJson('/title/' . $id . '/info');
}

function candidateScore(array $titleInfo, array $hit, string $title, string $originalTitle, ?int $year, string $mediaType): array
{
    $score = 0;
    $candidateType = is_string($titleInfo['type'] ?? null) ? $titleInfo['type'] : (string) ($hit['type'] ?? '');
    $typeMatches = $candidateType === $mediaType;

    if ($candidateType !== '') {
        $score += $typeMatches ? 400 : -500;
    }

    $candidateYear = isset($titleInfo['year']) && is_numeric($titleInfo['year'])
        ? (int) $titleInfo['year']
        : null;

    $yearDiff = null;

    if ($year !== null && $candidateYear !== null) {
        $yearDiff = abs($candidateYear - $year);

        if ($yearDiff === 0) {
            $score += 300;
        } elseif ($yearDiff === 1) {
            $score += 120;
        } elseif ($yearDiff === 2) {
            $score += 40;
        } else {
            $score -= min(300, $yearDiff * 60);
        }
    }

    $candidateTitles = array_values(array_unique(array_filter([
        is_string($titleInfo['title'] ?? null) ? $titleInfo['title'] : '',
        is_string($titleInfo['originalTitle'] ?? null) ? $titleInfo['originalTitle'] : '',
        is_string($hit['matchedTitle'] ?? null) ? $hit['matchedTitle'] : '',
    ])));

    $expectedTitles = array_values(array_unique(array_filter([$title, $originalTitle])));
    $bestTitleScore = 0;

    foreach ($expectedTitles as $expectedTitle) {
        foreach ($candidateTitles as $candidateTitle) {
            $bestTitleScore = max($bestTitleScore, titleMatchScore($candidateTitle, $expectedTitle));
        }
    }

    $score += $bestTitleScore;

    if (($hit['matchedLang'] ?? '') === FILMWEB_DEFAULT_LOCALE) {
        $score += 30;
    }

    return [
        'score' => $score,
        'titleScore' => $bestTitleScore,
        'typeMatches' => $typeMatches,
        'yearDiff' => $yearDiff,
        'candidateYear' => $candidateYear,
    ];
}

function isTrustedFilmwebMatch(array $assessment): bool
{
    $score = (int) ($assessment['score'] ?? 0);
    $titleScore = (int) ($assessment['titleScore'] ?? 0);
    $typeMatches = !empty($assessment['typeMatches']);
    $yearDiff = $assessment['yearDiff'] ?? null;

    if (!$typeMatches) {
        return false;
    }

    if ($titleScore <= 0) {
        return false;
    }

    if ($titleScore >= 600) {
        return $score >= 900;
    }

    if ($yearDiff !== null) {
        if ($yearDiff === 0 && $titleScore >= 220 && $score >= 900) {
            return true;
        }

        if ($yearDiff === 0 && $titleScore >= 160 && $score >= 860) {
            return true;
        }

        if ($yearDiff === 1 && $titleScore >= 250 && $score >= 770) {
            return true;
        }

        return false;
    }

    return $titleScore >= 250 && $score >= 650;
}

function filmwebGetMulti(array $urls): array
{
    if ($urls === []) {
        return [];
    }

    $result = [];
    $pending = $urls;

    for ($attempt = 0; $attempt < 3 && $pending !== []; $attempt += 1) {
        if ($attempt > 0) {
            usleep(250000 * $attempt);
        }

        $fetched = filmwebGetMultiOnce($pending);

        foreach ($fetched as $key => $payload) {
            $result[$key] = $payload;
        }

        $pending = array_diff_key($pending, $fetched);
    }

    return $result;
}

function filmwebGetMultiOnce(array $urls): array
{
    if ($urls === []) {
        return [];
    }

    if (!function_exists('curl_multi_init') || !function_exists('curl_init')) {
        $result = [];

        foreach ($urls as $key => $url) {
            try {
                [$statusCode, $body] = filmwebRequest((string) substr($url, strlen(FILMWEB_API_BASE)));

                if ($statusCode >= 200 && $statusCode < 300 && $body !== '') {
                    $payload = json_decode($body, true);

                    if (is_array($payload)) {
                        $result[$key] = $payload;
                    }
                }
            } catch (Throwable $exception) {
            }
        }

        return $result;
    }

    $multiHandle = curl_multi_init();
    $handles = [];
    $headers = filmwebHeaders();

    foreach ($urls as $key => $url) {
        $handle = curl_init($url);

        curl_setopt_array($handle, [
            CURLOPT_HTTPGET => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_HTTPHEADER => $headers,
        ]);

        curl_multi_add_handle($multiHandle, $handle);
        $handles[$key] = $handle;
    }

    do {
        $status = curl_multi_exec($multiHandle, $running);

        if ($running) {
            curl_multi_select($multiHandle, 1.0);
        }
    } while ($running && $status === CURLM_OK);

    $result = [];

    foreach ($handles as $key => $handle) {
        $body = curl_multi_getcontent($handle);
        $statusCode = (int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE);

        curl_multi_remove_handle($multiHandle, $handle);
        curl_close($handle);

        if ($statusCode < 200 || $statusCode >= 300 || !is_string($body) || $body === '') {
            continue;
        }

        $payload = json_decode($body, true);

        if (is_array($payload)) {
            $result[$key] = $payload;
        }
    }

    curl_multi_close($multiHandle);

    return $result;
}

function fetchTitleInfoBatch(array $ids): array
{
    $ids = array_values(array_unique(array_filter(
        array_map(static fn($id): int => (int) $id, $ids),
        static fn(int $id): bool => $id > 0
    )));

    if ($ids === []) {
        return [];
    }

    $urls = [];

    foreach ($ids as $id) {
        $urls[(string) $id] = FILMWEB_API_BASE . '/title/' . $id . '/info';
    }

    $result = [];

    foreach (filmwebGetMulti($urls) as $key => $payload) {
        $result[(int) $key] = $payload;
    }

    return $result;
}

function filmwebMatchQueries(string $title, string $originalTitle, ?int $year): array
{
    return array_values(array_unique(array_filter([
        trim(implode(' ', array_filter([$title, $year ? (string) $year : '']))),
        trim(implode(' ', array_filter([$originalTitle, $year ? (string) $year : '']))),
        $title,
        $originalTitle,
    ])));
}

function filmwebCandidatesFromSearchPayloads(array $searchPayloads, string $mediaType, int $maxCandidates): array
{
    $candidates = [];

    foreach ($searchPayloads as $searchPayload) {
        $searchHits = is_array($searchPayload['searchHits'] ?? null) ? $searchPayload['searchHits'] : [];

        foreach ($searchHits as $hit) {
            if (!is_array($hit)) {
                continue;
            }

            $hitId = isset($hit['id']) && is_numeric($hit['id']) ? (int) $hit['id'] : 0;
            $hitType = is_string($hit['type'] ?? null) ? $hit['type'] : '';

            if ($hitId <= 0 || $hitType !== $mediaType) {
                continue;
            }

            if (!isset($candidates[$hitId]) && count($candidates) < $maxCandidates) {
                $candidates[$hitId] = $hit;
            }
        }
    }

    return $candidates;
}

function filmwebResolveBestMatch(array $candidates, array $titleInfoById, string $title, string $originalTitle, ?int $year, string $mediaType, string $fallbackUrl, ?array &$matchedTitleInfo = null): array
{
    $bestMatch = null;

    foreach ($candidates as $hit) {
        $hitId = (int) $hit['id'];

        if (!isset($titleInfoById[$hitId])) {
            continue;
        }

        $titleInfo = $titleInfoById[$hitId];
        $assessment = candidateScore($titleInfo, $hit, $title, $originalTitle, $year, $mediaType);

        if ($bestMatch === null || $assessment['score'] > $bestMatch['assessment']['score']) {
            $bestMatch = [
                'id' => $hitId,
                'assessment' => $assessment,
                'titleInfo' => $titleInfo,
                'hit' => $hit,
            ];
        }
    }

    if ($bestMatch === null || !isTrustedFilmwebMatch($bestMatch['assessment'])) {
        return [
            'id' => null,
            'url' => $fallbackUrl,
        ];
    }

    $titleInfo = $bestMatch['titleInfo'];
    $matchedTitleInfo = $titleInfo;
    $resolvedTitle = (string) ($titleInfo['title'] ?? $titleInfo['originalTitle'] ?? $title);
    $resolvedYear = isset($titleInfo['year']) && is_numeric($titleInfo['year'])
        ? (int) $titleInfo['year']
        : ($year ?? 0);
    $resolvedType = is_string($titleInfo['type'] ?? null) ? $titleInfo['type'] : $mediaType;

    if (!in_array($resolvedType, FILMWEB_ALLOWED_MEDIA_TYPES, true)) {
        return [
            'id' => null,
            'url' => $fallbackUrl,
        ];
    }

    if ($resolvedTitle === '' || $resolvedYear <= 0) {
        return [
            'id' => $bestMatch['id'],
            'url' => $fallbackUrl,
        ];
    }

    return [
        'id' => $bestMatch['id'],
        'url' => filmwebEntityUrl($resolvedType, $resolvedTitle, $resolvedYear, $bestMatch['id']),
    ];
}

function matchFilmwebTitle(string $title, string $originalTitle, ?int $year, string $mediaType, ?array &$matchedTitleInfo = null): array
{
    $queries = filmwebMatchQueries($title, $originalTitle, $year);
    $fallbackUrl = filmwebSearchUrl($title !== '' ? $title : $originalTitle, $year);

    $searchUrls = [];

    foreach ($queries as $index => $query) {
        $searchUrls[(string) $index] = FILMWEB_API_BASE . '/search?' . http_build_query(['query' => $query]);
    }

    $searchPayloads = [];

    foreach (filmwebGetMulti($searchUrls) as $index => $payload) {
        $searchPayloads[(int) $index] = $payload;
    }

    $candidates = filmwebCandidatesFromSearchPayloads($searchPayloads, $mediaType, 10);

    $titleInfoById = fetchTitleInfoBatch(
        array_map(static fn(array $hit): int => (int) $hit['id'], $candidates)
    );

    return filmwebResolveBestMatch(
        $candidates,
        $titleInfoById,
        $title,
        $originalTitle,
        $year,
        $mediaType,
        $fallbackUrl,
        $matchedTitleInfo
    );
}

function matchFilmwebBatch(array $items): array
{
    $prepared = [];
    $searchUrls = [];

    foreach ($items as $i => $item) {
        if (!is_array($item)) {
            $prepared[$i] = null;
            continue;
        }

        $title = is_string($item['title'] ?? null) ? trim($item['title']) : '';
        $originalTitle = is_string($item['originalTitle'] ?? null) ? trim($item['originalTitle']) : '';
        $year = isset($item['year']) && preg_match('/^\d{4}$/', (string) $item['year']) ? (int) $item['year'] : null;
        $mediaType = is_string($item['mediaType'] ?? null) ? $item['mediaType'] : '';

        if (($title === '' && $originalTitle === '') || !in_array($mediaType, FILMWEB_ALLOWED_MEDIA_TYPES, true)) {
            $prepared[$i] = ['fallbackUrl' => filmwebSearchUrl($title !== '' ? $title : $originalTitle, $year)];
            continue;
        }

        $queries = filmwebMatchQueries($title, $originalTitle, $year);

        $prepared[$i] = [
            'title' => $title,
            'originalTitle' => $originalTitle,
            'year' => $year,
            'mediaType' => $mediaType,
            'queries' => $queries,
            'fallbackUrl' => filmwebSearchUrl($title !== '' ? $title : $originalTitle, $year),
        ];

        foreach ($queries as $qi => $query) {
            $searchUrls[$i . "\t" . $qi] = FILMWEB_API_BASE . '/search?' . http_build_query(['query' => $query]);
        }
    }

    $searchResults = filmwebGetMulti($searchUrls);

    $itemCandidates = [];
    $allIds = [];

    foreach ($prepared as $i => $info) {
        if ($info === null || !isset($info['queries'])) {
            continue;
        }

        $payloads = [];

        foreach ($info['queries'] as $qi => $query) {
            $payloads[$qi] = $searchResults[$i . "\t" . $qi] ?? [];
        }

        $candidates = filmwebCandidatesFromSearchPayloads($payloads, $info['mediaType'], 10);
        $itemCandidates[$i] = $candidates;

        foreach ($candidates as $hitId => $hit) {
            $allIds[] = (int) $hitId;
        }
    }

    $titleInfoById = fetchTitleInfoBatch($allIds);

    $results = [];

    foreach ($prepared as $i => $info) {
        if ($info === null) {
            $results[] = ['id' => null, 'url' => ''];
            continue;
        }

        if (!isset($info['queries'])) {
            $results[] = ['id' => null, 'url' => $info['fallbackUrl']];
            continue;
        }

        $results[] = filmwebResolveBestMatch(
            $itemCandidates[$i] ?? [],
            $titleInfoById,
            $info['title'],
            $info['originalTitle'],
            $info['year'],
            $info['mediaType'],
            $info['fallbackUrl']
        );
    }

    return $results;
}

function providerLogoUrl(array $providerMeta): string
{
    $path = trim((string) ($providerMeta['path'] ?? ''));

    if ($path === '') {
        return '';
    }

    return FILMWEB_PROVIDER_LOGO_BASE . str_replace('$', '1', $path);
}

function groupLabelForType(string $typeKey): ?string
{
    return match ($typeKey) {
        'subscription' => 'Abonament',
        'watchFree' => 'Za darmo',
        'watchWithAds' => 'Z reklamami',
        'buy' => 'Kup',
        'rent' => 'Wypożycz',
        'buyOrRent' => 'Kup / wypożycz',
        'onlineTelevision' => 'Telewizja online',
        default => null,
    };
}

function providerTypeFromPayment(array $payments): string
{
    $hasSubscription = false;
    $hasFree = false;
    $hasAds = false;
    $hasInternetTv = false;
    $hasBuy = false;
    $hasRent = false;

    foreach ($payments as $payment) {
        if (!is_array($payment)) {
            continue;
        }

        $hasSubscription = $hasSubscription || !empty($payment['subscription']);
        $hasFree = $hasFree || !empty($payment['free']);
        $hasAds = $hasAds || !empty($payment['hasAds']);
        $hasInternetTv = $hasInternetTv || !empty($payment['internetTv']);
        $hasBuy = $hasBuy || !empty($payment['buy']);
        $hasRent = $hasRent || !empty($payment['rent']);
    }

    if ($hasSubscription) {
        return 'subscription';
    }

    if ($hasFree && $hasAds) {
        return 'watchWithAds';
    }

    if ($hasFree) {
        return 'watchFree';
    }

    if ($hasInternetTv) {
        return 'onlineTelevision';
    }

    if ($hasBuy && $hasRent) {
        return 'buyOrRent';
    }

    if ($hasBuy) {
        return 'buy';
    }

    if ($hasRent) {
        return 'rent';
    }

    return 'unknown';
}

function providerHasSubscriptionPlan(array $providerMeta): bool
{
    $subscriptionPrices = trim((string) ($providerMeta['abonaments'] ?? ''));
    $planPrices = is_array($providerMeta['abonamentPricesPerPlan'] ?? null)
        ? $providerMeta['abonamentPricesPerPlan']
        : [];

    return $subscriptionPrices !== '' || $planPrices !== [];
}

function selectPaymentsGroup(array $payments, string $groupType): ?array
{
    $groupedPayments = [];

    foreach ($payments as $payment) {
        if (!is_array($payment)) {
            continue;
        }

        $groupKey = trim((string) ($payment['paymentType'] ?? ''));

        if ($groupKey === '') {
            $groupKey = 'default';
        }

        if (!isset($groupedPayments[$groupKey])) {
            $groupedPayments[$groupKey] = [];
        }

        $groupedPayments[$groupKey][] = $payment;
    }

    foreach ($groupedPayments as $groupItems) {
        $paymentTypes = array_values(array_unique(array_filter(array_map(
            static fn (array $item): string => trim((string) ($item['paymentType'] ?? '')),
            $groupItems
        ))));
        $type = providerTypeFromPayment($groupItems);

        if ($groupType === 'buyOrRent' && in_array($type, ['buy', 'rent', 'buyOrRent'], true)) {
            return [
                'type' => $type,
                'link' => trim((string) ($groupItems[0]['paymentUrl'] ?? '')),
            ];
        }

        if ($groupType === 'watchOrFree' && $type === 'subscription') {
            return [
                'type' => $type,
                'link' => trim((string) ($groupItems[0]['paymentUrl'] ?? '')),
            ];
        }

        if ($groupType === 'watchOnlineTelevision' && $type === 'onlineTelevision') {
            return [
                'type' => $type,
                'link' => trim((string) ($groupItems[0]['paymentUrl'] ?? '')),
            ];
        }

        if ($groupType === 'freeWithAds' && $type === 'watchWithAds') {
            return [
                'type' => $type,
                'link' => trim((string) ($groupItems[0]['paymentUrl'] ?? '')),
            ];
        }
    }

    return null;
}

function addProviderToGroups(array &$groups, string $typeKey, int $providerId, array $providerMeta, array $offer, ?array $selectedGroup): void
{
    $label = groupLabelForType($typeKey);

    if ($label === null) {
        return;
    }

    if (!isset($groups[$label])) {
        $groups[$label] = [];
    }

    $link = '';

    if ($selectedGroup !== null) {
        $link = trim((string) ($selectedGroup['link'] ?? ''));
    }

    if ($link === '') {
        $link = trim((string) ($offer['link'] ?? ''));
    }

    if ($link === '') {
        $link = trim((string) ($providerMeta['link'] ?? ''));
    }

    $groups[$label][$providerId] = [
        'name' => trim((string) ($providerMeta['displayName'] ?? $providerMeta['name'] ?? 'Nieznany serwis')),
        'logo' => providerLogoUrl($providerMeta),
        'priority' => isset($providerMeta['order']) && is_numeric($providerMeta['order'])
            ? (int) $providerMeta['order']
            : 9999,
        'url' => $link,
    ];
}

function normalizeFilmwebProviders(array $offers, array $providers, ?string $vodPageUrl): array
{
    $providerMap = [];

    foreach ($providers as $provider) {
        if (!is_array($provider) || !isset($provider['id']) || !is_numeric($provider['id'])) {
            continue;
        }

        $providerMap[(int) $provider['id']] = $provider;
    }

    $groups = [];

    foreach ($offers as $offer) {
        if (!is_array($offer)) {
            continue;
        }

        $providerId = isset($offer['vodProvider']) && is_numeric($offer['vodProvider'])
            ? (int) $offer['vodProvider']
            : 0;

        if ($providerId <= 0 || !isset($providerMap[$providerId])) {
            continue;
        }

        $providerMeta = $providerMap[$providerId];
        $payments = is_array($offer['payments'] ?? null) ? $offer['payments'] : [];

        $subscription = $payments !== []
            ? selectPaymentsGroup($payments, 'watchOrFree')
            : (providerHasSubscriptionPlan($providerMeta)
                ? [
                    'type' => 'subscription',
                    'link' => trim((string) ($offer['link'] ?? '')),
                ]
                : null);

        if ($subscription !== null) {
            addProviderToGroups($groups, $subscription['type'], $providerId, $providerMeta, $offer, $subscription);
        }
    }

    $finalGroups = [];

    foreach ($groups as $label => $providersById) {
        $providersList = array_values($providersById);

        usort($providersList, static function (array $left, array $right): int {
            if (($left['priority'] ?? 9999) !== ($right['priority'] ?? 9999)) {
                return ($left['priority'] ?? 9999) <=> ($right['priority'] ?? 9999);
            }

            return strcmp((string) ($left['name'] ?? ''), (string) ($right['name'] ?? ''));
        });

        if ($providersList !== []) {
            $finalGroups[$label] = $providersList;
        }
    }

    return [
        'hasData' => $finalGroups !== [],
        'link' => $vodPageUrl,
        'groups' => $finalGroups,
        'source' => 'filmweb',
        'attributionLabel' => null,
        'attributionUrl' => null,
    ];
}

function providersPayloadForTitle(int $id, ?array $titleInfo = null): array
{
    if ($titleInfo === null) {
        $titleInfo = fetchTitleInfo($id);
    }

    $type = is_string($titleInfo['type'] ?? null) ? $titleInfo['type'] : 'film';

    if (!in_array($type, FILMWEB_ALLOWED_MEDIA_TYPES, true)) {
        $type = 'film';
    }

    $lists = filmwebGetMulti([
        'offers' => FILMWEB_API_BASE . '/vod/' . $type . '/' . $id . '/providers/list',
        'providers' => FILMWEB_API_BASE . '/vod/providers/list',
    ]);

    $offers = $lists['offers'] ?? [];
    $providers = $lists['providers'] ?? [];

    $title = (string) ($titleInfo['title'] ?? $titleInfo['originalTitle'] ?? '');
    $year = isset($titleInfo['year']) && is_numeric($titleInfo['year']) ? (int) $titleInfo['year'] : 0;
    $vodPageUrl = ($title !== '' && $year > 0)
        ? filmwebEntityUrl($type, $title, $year, $id) . '/vod'
        : null;

    return normalizeFilmwebProviders(
        is_array($offers) ? $offers : [],
        is_array($providers) ? $providers : [],
        $vodPageUrl
    );
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
    jsonResponse(405, ['error' => 'Dozwolona jest tylko metoda GET.']);
}

$action = requestValue('action');

if ($action === '') {
    $action = requestValue('id') !== '' ? 'providers' : 'match';
}

try {
    if ($action === 'match') {
        $title = requestValue('title');
        $originalTitle = requestValue('originalTitle');
        $year = requestYear(requestValue('year'));
        $mediaType = requestValue('mediaType');

        if ($title === '' && $originalTitle === '') {
            jsonResponse(400, ['error' => 'Brak tytułu do dopasowania Filmweb.']);
        }

        if (!in_array($mediaType, FILMWEB_ALLOWED_MEDIA_TYPES, true)) {
            jsonResponse(400, ['error' => 'Nieprawidłowy typ materiału Filmweb.']);
        }

        $matchedTitleInfo = null;
        $match = matchFilmwebTitle($title, $originalTitle, $year, $mediaType, $matchedTitleInfo);

        if (requestValue('providers') === '1') {
            $matchedId = (int) ($match['id'] ?? 0);

            $match['providers'] = $matchedId > 0
                ? providersPayloadForTitle($matchedId, $matchedTitleInfo)
                : null;
        }

        jsonResponse(200, $match);
    }

    if ($action === 'matchBatch') {
        $rawItems = $_GET['items'] ?? '';
        $decoded = is_string($rawItems) ? json_decode($rawItems, true) : null;

        if (!is_array($decoded)) {
            jsonResponse(400, ['error' => 'Brak listy tytułów do dopasowania Filmweb.']);
        }

        jsonResponse(200, ['results' => matchFilmwebBatch($decoded)]);
    }

    if ($action === 'providers') {
        $id = (int) requestValue('id');

        if ($id <= 0) {
            jsonResponse(400, ['error' => 'Brak prawidłowego identyfikatora Filmweb.']);
        }

        jsonResponse(200, providersPayloadForTitle($id));
    }

    if ($action === 'premieres') {
        $now = new DateTimeImmutable('first day of this month');
        $kind = filmwebPremiereKind();
        $year = requestIntValue('year', (int) $now->format('Y'), 1900, 2100);
        $month = requestIntValue('month', (int) $now->format('n'), 1, 12);

        $payload = requestValue('engine') === 'php'
            ? null
            : premiereMonthPayloadWithScrapling($kind, $year, $month);

        if (!is_array($payload)) {
            $payload = premiereMonthPayload($kind, $year, $month);
            $payload['engine'] = 'php';
        }

        $payload['cache'] = 'fresh';

        jsonResponse(200, $payload);
    }
    jsonResponse(400, ['error' => 'Nieprawidłowa akcja Filmweb.']);
} catch (Throwable $exception) {
    jsonResponse(502, ['error' => $exception->getMessage()]);
}
