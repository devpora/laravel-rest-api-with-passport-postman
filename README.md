# Step-by-step example for Rest-API with Laravel 10

## EN
## (English)
All data is in separate branches

## SK
[Guide](#en)
## (Slovensky)
Všetky dáta sú v samostatných vetvách
[Návod](#sk)

---
## en

## Development
```bash
cp .env.example .env # - copy environment file
composer install # - install dependencies
php artisan key:generate # - generate application key
```
Change database section in .env with your db connection data.

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```
```bash
php artisan migrate # - creating tables in the database
php artisan passport:install # - create the encryption keys to generate secure access tokens. 
php artisan serve # - start development server on http://localhost:8000
```

## Changes
- Added oAuth login for Google, GitHub, GitLab

## Release notes
- v004 - add oAuth
- v003 - add auth
- v002 - add json response
- v001 - add Laravel 10

## Other:
### Documentation:
[Laravel 10 documentation](https://laravel.com/docs/10.x)
[Passport documentation](https://laravel.com/docs/10.x/passport)

### Data:
[Google](https://console.cloud.google.com)
[GitHub](https://github.com/settings/apps)
[GitLab](https://gitlab.com/profile/applications)

# How create ClientId a ClientSecret
Google:
- Where: Navigation Menu->APIs & Services->OAuth (https://console.cloud.google.com/apis/credentials)
- How: Create Credentials->oAuth Client ID: Type Web, Redirect URI: URL/oauth/callback/google
-
GitHub:
- Where: Settings->Developer settings->oAuth (https://github.com/settings/apps)
- How: Callback url: URL/oauth/callback/github

GitLab:
- Where: Preferences->Application (https://gitlab.com/-/profile/applications)
- How: Redirect uri: URL/oauth/callback/gitlab Scopes: read_user


---

## sk

## Vývoj
```bash
cp .env.example .env # - prekopírovanie súboru s nastaveniami projektu
composer install # - inštalácia balíkov
php artisan key:generate # - vygenerovanie aplikačného kľúča
```
Upravte v .env informácie potrebné na pripojenie do databázy

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```
```bash
php artisan migrate # - vytvorenie tabuliek v databáze
php artisan passport:install # - vytvorenie šifrovacích kľúčov na generovanie tokenov
php artisan serve # - spustenie servera na http://localhost:8000
```

## Zmeny
- Pridaná podpora pre oAuth cez Google, GitHub, GitLab

## Poznámky k vydaniu
- v004 - pridané prihlasovanie cez oAuth
- v003 - pridaná podpora autentifikácie
- v002 - pridaný json response
- v001 - pridaný Laravel 10

## Ostatné:
### Dokumentácia:
[Laravel 10 dokumentácia](https://laravel.com/docs/10.x)
[Passport dokumentácia](https://laravel.com/docs/10.x/passport)

### Dáta:
[Google](https://console.cloud.google.com)
[GitHub](https://github.com/settings/apps)
[GitLab](https://gitlab.com/profile/applications)

# Ako vytvoriť ClientId a ClientSecret
Google:
- Kde: Navigation Menu->APIs & Services->OAuth (https://console.cloud.google.com/apis/credentials)
- Ako: Create Credentials->oAuth Client ID: Type Web, Redirect URI: URL/oauth/callback/google

GitHub:
- Kde: Settings->Developer settings->oAuth (https://github.com/settings/apps)
- Ako: Callback url: URL/oauth/callback/github

GitLab:
- Kde: Preferences->Application (https://gitlab.com/-/profile/applications)
- Ako: Redirect uri: URL/oauth/callback/gitlab Scopes: read_user
