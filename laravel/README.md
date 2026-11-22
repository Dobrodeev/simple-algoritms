# Binance Traiding на Laravel

Тестовое задание из `Test Binance Traiding.docx` на Laravel 10. Хостинг не используется: база данных MySQL создаётся локально.

## Что сделано

| Пункт задания | Реализация |
|---|---|
| БД (локально вместо хостинга) | MySQL, миграция `courses` (`symbol`, `price`) |
| Загрузить `courses.json` в БД | `CourseSeeder` из `database/data/courses.json` |
| Таблица с данными из БД | `/` — `CourseController@index` |
| DataTables с сортировкой | jQuery DataTables 1.13 во всех таблицах |
| Форма добавления в БД | `/` — `POST /courses` |
| Форма добавления в JSON-файл | `/json` — `POST /json` (рабочая копия: `storage/app/courses.json`) |
| Курсы валют с Binance по API | `/binance` — `GET https://api.binance.com/api/v3/ticker/price` |
| Подсветка пар | `…BTC` — зелёный, `…ETH` — жёлтый, `…USDT` — синий |

## Требования

- PHP 8.1+ с расширениями `pdo_mysql`, `mbstring`, `openssl`, `curl`, `fileinfo`
- Composer
- MySQL 8 (локально)

## Установка

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Создайте локальную базу:

```sql
CREATE DATABASE binance_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Укажите в `.env` параметры подключения (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`), затем создайте таблицу и загрузите JSON:

```bash
php artisan migrate --seed
php artisan serve
```

Откройте http://127.0.0.1:8000.

## Тесты

```bash
php artisan test
```

Тесты используют SQLite в памяти и не трогают MySQL; запросы к Binance подменяются через `Http::fake()`.

## Если Binance API не отвечает

Ошибка `cURL error 60: SSL certificate problem` означает, что в `php.ini` не указан файл корневых сертификатов. В OSPanel он уже есть — `C:\OSPanel\userdata\config\cacert.pem`; иначе скачайте [cacert.pem](https://curl.se/docs/caextract.html). Укажите его в `php.ini`:

```ini
curl.cainfo = "C:\path\to\cacert.pem"
openssl.cafile = "C:\path\to\cacert.pem"
```
