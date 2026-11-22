# Binance Traiding на Laravel

Тестовое задание из `Test Binance Traiding.docx` на Laravel 13 (PHP 8.6) с админкой Filament 5. Хостинг не используется: база данных создаётся локально (MariaDB или MySQL).

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

## Админ-панель

[Filament 5](https://filamentphp.com/) на http://127.0.0.1:8000/admin — список курсов с поиском, сортировкой, фильтром по валюте котировки (BTC / ETH / USDT), создание, редактирование и удаление.

Войти может только пользователь из `ADMIN_EMAIL`. Логин и пароль задаются в `.env` (`ADMIN_EMAIL`, `ADMIN_PASSWORD`; `start.ps1` при первом запуске генерирует пароль сам). Создать или обновить пользователя:

```bash
php artisan db:seed --class=AdminUserSeeder
```

Для Filament нужно PHP-расширение `intl`.

## Требования

- PHP 8.3+ (проверено на 8.6) с расширениями `pdo_mysql`, `intl`, `mbstring`, `openssl`, `curl`, `fileinfo`
- Composer
- MySQL 8 (локально)

## Быстрый запуск (Windows)

Из корня репозитория:

```powershell
.\start.ps1
```

Скрипт берёт PHP и `composer.phar` из папки `php` в PATH (например, PHP 8.6 из winget), MariaDB 10.8 — из `C:\OSPanel`; при первом запуске создаёт базу в `.mariadb\data` (порт 3307, `root` без пароля), ставит зависимости, выполняет миграции и загрузку `courses.json`, затем запускает сайт на http://127.0.0.1:8000. Остановка — Ctrl+C. Параметры: `-Port`, `-PhpDir`, `-Composer`, `-MariaDbDir`, `-KeepDatabase`. Нужные расширения PHP включаются в `php.ini` рядом с `php.exe`.

Если PowerShell блокирует запуск скриптов: `powershell -ExecutionPolicy Bypass -File .\start.ps1`.

## Установка вручную

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
