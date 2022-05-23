## Prerequisites

-   PHP >= 7.3
-   MySql

## Steps to up running the projects Laravel

-   git clone `https://github.com/rsvijay2009/whether-api.git`

-   Change `DB_DATABASE` variable in the `.env` to point your local database

-   Add `OPEN_WHETHER_API_KEY` variable in the `.env`

-   cd whether-api `composer install`

-   `php artisan migrate`

-   `php artisan serve`
