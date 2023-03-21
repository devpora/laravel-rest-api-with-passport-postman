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
php artisan serve # - start development server on http://localhost:8000
```

## Changes
- Set default json response
- Welcome page has 2 links. 404 and Exception. Both returns response in json. For show only mesage, you can disable debug mode
```bash
APP_DEBUG=false
```


## Release notes
- v002 - add json response
- v001 - add Laravel 10

## Other:
[Laravel 10 documentation](https://laravel.com/docs/10.x)


---

## sk

## Vývoj
```bash
cp .env.example .env # - prekopírovanie súboru s nastaveniami projektu
composer install # - inštalácia balíkov
php artisan key:generate # - vygenerovanie aplikačného kľúča
php artisan serve # - spustenie servera na http://localhost:8000
```

## Zmeny
- Nastavené json odpovede ako predvolené
- Uvítacia stránka má 2 odkazy. 404 a Exception. Obidve vrátia odpoveď v json. Ak chcete zobraziť iba správu, môžete vypnúť režim ladenia
```bash
APP_DEBUG=false
```

## Poznámky k vydaniu
- v002 - pridaný json response
- v001 - pridaný Laravel 10

## Ostatné:
[Laravel 10 dokumentácia](https://laravel.com/docs/10.x)

