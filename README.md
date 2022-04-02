## Тестовое задание

### Использовано:

- **[php 8](https://www.php.net/releases/8.0/)**
- **[laravel 8.83](https://laravel.com/)**
- **[sanctum](https://laravel.com/docs/9.x/sanctum)**
- **[mysql 5.7](https://dev.mysql.com/downloads/mysql/5.7.html)**
- **[scribe](https://scribe.knuckles.wtf/)**
- **[mailbase](https://github.com/tkeer/mailbase/)**

После клонирования:

-cd project-folder

-cp .env.example .env

-php artisan key:generate

#### В файле .env нужно настроить:
APP_*

DB_*

А также MAIL_MAILER=mailbase для пакета заглушки

#### Далее:
-composer install

-php artisan migrate

-php artisan scribe:generate

### Песочница /docs
### Письма /mailbase


Авторизация упрощена, все токены ползьователя отзываются каждый раз при логине и дается новый.
Создание отвественных работников упрощено и доступно по публичному апи.
