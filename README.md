## About Reqbag

Working on two recent clients projects I had to deal with webhooks/callbacks from and to 3rd party APIs and while using external tools like Pipedream or [Request Baskets](https://rbaskets.in/) on the first project wasn't an issue, for the second one external tools were not allowed due to security policy. I didn't want a local/staging app written in Go or running in Docker and being a huge fan of Laravel, I quickly implemented a basic functionality with a fresh Laravel installation and spinned up a host on our staging server which I called "Reqbag", that was enough.
However once the project was finished I decided to spend few days refactoring and enhancing reqbag a bit and open-source it so it could be useful for someone else or serve a kind of simple demo application.

Feel free to create any issues / pull requests.

## Tech stack
- [Laravel 11](http://laravel.com/docs/11.x)
- [Filament 3](https://filamentphp.com/docs/3.x/)
- [SQLite](http://sqlite.org)
- [OpenAI PHP for Laravel](https://github.com/openai-php/laravel)
- [Pest](https://pestphp.com/docs/) and [Larastan](https://github.com/larastan/larastan)


## Setup guide
- `git clone git@github.com:plakhin/reqbag.git && cd reqbag`
- `composer install`
- `cp .env.example .env`
- `php artisan key:generate`
- edit `.env` values
- `touch database/database.sqlite`
- `php artisan migrate`
- use [Laravel Herd](herd.laravel.com) or any other local web server to serve the app
- open http://reqbag.test (or whatever url you set in `.env`) in the browser and create a first bag
- now you can send HTTP request to http://`{bag}`.reqbag.test, they will be saved in the DB and listed on the homepage.

## License

Reqbag is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
