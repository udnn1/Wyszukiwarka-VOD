<div align="center">

# Na jakim VOD?

Lekka wyszukiwarka filmów i seriali pokazująca, gdzie dany tytuł jest dostępny online.

![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=for-the-badge&logo=php&logoColor=white&labelColor=474A8A)
![JavaScript](https://img.shields.io/badge/JavaScript-vanilla-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black&labelColor=323330)
![HTML5](https://img.shields.io/badge/HTML5-markup-E34F26?style=for-the-badge&logo=html5&logoColor=white&labelColor=A8341B)
![CSS3](https://img.shields.io/badge/CSS3-styles-1572B6?style=for-the-badge&logo=css3&logoColor=white&labelColor=0E4C82)
![Python](https://img.shields.io/badge/Python-3-3776AB?style=for-the-badge&logo=python&logoColor=white&labelColor=FFD43B)

![TMDB](https://img.shields.io/badge/TMDB-API-01B4E4?style=for-the-badge&logo=themoviedatabase&logoColor=white&labelColor=0D253F)
![Filmweb](https://img.shields.io/badge/Filmweb-data-FFD000?style=for-the-badge&logo=filmweb&logoColor=black&labelColor=E0B400)
![Upflix](https://img.shields.io/badge/Upflix-nowości-7C3AED?style=for-the-badge&logo=netflix&logoColor=white&labelColor=4C1D95)
![VOD](https://img.shields.io/badge/VOD-search-FF6B35?style=for-the-badge&logo=plex&logoColor=white&labelColor=C2410C)

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

## Wkład AI

Claude (Anthropic) wykonał wyłącznie **code review** — przegląd kodu. Nie jest autorem ani współautorem kodu projektu.
