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
- Added seeder to create default user
- Added laravel/passport package for authentication
- Added functions for login/register/logout with input validation
- Added routes for test authorized/unauthorized access
- Added data for simple testing via Postman (collection and environment)

## Release notes
- v003 - add auth
- v002 - add json response
- v001 - add Laravel 10

## Other:
[Laravel 10 documentation](https://laravel.com/docs/10.x)
[Passport documentation](https://laravel.com/docs/10.x/passport)


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
- Pridaný seeder na vytvorenie defaultného používateľa
- Pridaný balík laravel/passport na autentifikáciu
- Pridané funkcie na login/register/logout s validáciou vstupu
- Pridané cesty na testovanie autorizovaného/neautorizovaného prístupu
- Pridané dáta na jednoduché testovanie cez Postman (collection a environment)

## Poznámky k vydaniu
- v003 - pridaná podpora autentifikácie
- v002 - pridaný json response
- v001 - pridaný Laravel 10

## Ostatné:
[Laravel 10 dokumentácia](https://laravel.com/docs/10.x)
[Passport dokumentácia](https://laravel.com/docs/10.x/passport)
