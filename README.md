<div align="center">

# Na jakim VOD?

Lekka wyszukiwarka filmów i seriali pokazująca, gdzie dany tytuł jest dostępny online.

![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-vanilla-F7DF1E?style=for-the-badge&logo=javascript&logoColor=111)
![TMDB](https://img.shields.io/badge/TMDB-API-01B4E4?style=for-the-badge)
![VOD](https://img.shields.io/badge/VOD-search-FF8A3D?style=for-the-badge)

</div>

## Opis

**Na jakim VOD?** to prosta aplikacja webowa do sprawdzania dostępności filmów i seriali na platformach streamingowych. Projekt łączy dane katalogowe z TMDB, informacje z Filmwebu oraz aktualności VOD z Upflix, dzięki czemu pozwala szybko znaleźć tytuł, sprawdzić jego opis, plakat, typ produkcji i dostępne serwisy.

## Najważniejsze funkcje

- wyszukiwanie filmów i seriali po tytule
- sprawdzanie dostępności VOD dla wybranego regionu
- linki do kart tytułów w Filmwebie
- sekcje nowości dla Netflix i HBO Max
- losowanie propozycji do obejrzenia
- obsługa filmów, seriali, kolekcji i szczegółów produkcji
- responsywny interfejs dla desktopu i urządzeń mobilnych
- lokalne proxy zabezpieczające zapytania do zewnętrznych usług

## Stack technologiczny

- **PHP 8.1+**
- **Vanilla JavaScript**
- **HTML / CSS**
- **TMDB API**
- **Filmweb**
- **Upflix**
- **Python 3 + Scrapling** jako opcjonalna warstwa pomocnicza

## Wymagania

- PHP 8.1 lub nowszy
- rozszerzenia PHP: `curl`, `json`, `dom`, `mbstring`
- konto i klucz API TMDB
- opcjonalnie Python 3, jeśli ma działać most Scrapling

## Uruchomienie lokalne

Sklonuj repozytorium lub skopiuj pliki do katalogu projektu.

```bash
git clone <adres-repozytorium>
cd <nazwa-repozytorium>
```

Uruchom lokalny serwer PHP:

```bash
php -S localhost:8000
```

Otwórz aplikację w przeglądarce:

```text
http://localhost:8000
```

## Konfiguracja TMDB

Projekt korzysta z TMDB API. Utwórz lokalny plik `tmdb-auth.json` w katalogu projektu:

```json
{
  "bearer": "twoj_token_bearer",
  "apiKey": "twoj_klucz_api"
}
```

```bash
TMDB_API_BEARER_TOKEN=twoj_token_bearer
TMDB_API_KEY=twoj_klucz_api
```

Obsługiwane aliasy:

```bash
TMDB_BEARER_TOKEN
TMDB_V3_API_KEY
```

Nie umieszczaj prywatnych kluczy API bezpośrednio w plikach śledzonych przez git.

## Opcjonalny most Scrapling

Most Scrapling uruchamia skrypt Python z poziomu PHP i pomaga pobierać dane z serwisów, które wymagają dokładniejszego parsowania HTML.

Instalacja zależności:

```bash
python -m pip install scrapling
```

Opcjonalne zmienne:

```bash
SCRAPLING_PYTHON=python
SCRAPLING_DISABLED=1
```

`SCRAPLING_DISABLED=1` wyłącza warstwę Scrapling i pozostawia standardowe mechanizmy PHP.

## Endpointy

| Plik | Rola |
| --- | --- |
| `tmdb-proxy.php` | proxy do wyszukiwania, odkrywania i pobierania szczegółów z TMDB |
| `filmweb-proxy.php` | dopasowanie tytułów, linki Filmweb i dane o dostępności |
| `upflix-proxy.php` | pobieranie nowości z platform VOD |
| `scrapling-bridge.php` | uruchamianie pomocniczego skryptu Python z poziomu PHP |
| `scrapling-vod-bridge.py` | parser danych dla Upflix i Filmweb |
