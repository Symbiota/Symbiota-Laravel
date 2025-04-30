# Setting Up a Development Enviroment
To setup a development enviroment you have two options docker or native.

## When to Choose Docker
If you have to manage multiple php versions or databases spread across different projects or you swap machines often using the docker development enviroment will suit you best.

## When to Choose Native
Native will generally be "easier" if you are unfamiliar with containerization concepts and there can be issues with docker on windows and/or apple silicion. These ease of use will come at the cost of keeping your php version up to date and ensuring you have the correct extensions enabled.

## Enviroment Variables
First create an `.env` file based upon `.env.example` or `.env.docker-example` depending on what enviroment you are setting up.

# Docker Enviroment
To setup the docker enviroment make sure you have `docker` and `docker-compose` installed. See docker's documenation for doing so.

Once Docker is installed you simply need to navigate a terminal to the project directory and run `docker compose up`. Running this with the `-d` flag to detach and let it run in th background.

If you wish to run only the php application within docker and use a native database or another container in a different network then some addition steps are required.

First change the enviroment variable `DB_HOST` to `host.docker.internal` located in `.env`. This will tell docker to look for other service running locally on the host machine.

Then change the `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` as needed to match the external database.

Lastly change the command to only include the services you wish to run. If you only want to run `web` and `memcached` service then use `docker compose up web memcached -d`

To get hot reloading for the container you must run `npm run dev` and if you add new javascript libraries you will need to run `docker compose build` in order save them in the container.

Note: [lazydocker](https://github.com/jesseduffield/lazydocker) is an amazing tool to manage containers while doing development.

# Native Enviroment
Next make sure you have at least [laravel server requirements](https://laravel.com/docs/12.x/deployment#server-requirements) met.

Other extensions that need enabling are
- mysqli
- imagick | gd
- zip
- xdebug (If desired)

Before Running anything

Once all extensions are installed, run `composer install` to download php vendor files and `npm run build` to get the javascript and css builds.

You can run the server by using `php artisan serve` and you can run page hot reloading with `npm run dev`.

Note: If you have an existing php setup for Symbiota you only need to make sure the laravel requirements are met.

# Setup App Key
Laravel requires an `APP_KEY` enviroment variable to be set and you can generate one by using the command `php artisan key:generate`. If you want to run this in a docker container you can do so via `docker exec [container name] php artisan key:generate` 

# Database
Before proceeding make sure you can load the home page.

## Existing Symbiota Schema Instance
If you have an existing Symbiota Schema first upgrade it to the latest version found in the [Symbiota Repository](https://github.com/Symbiota/Symbiota). Then run the sql file `./database/schema/upgrade.sql` which will add the migrations already present in the existing Symbiota Schema and prevent the migration script from trying to add tables that are already present.

## Migrations
To setup the missing database tables run the database migrations see [Laravel Migrations](https://laravel.com/docs/12.x/migrations) for more information

### Native
To run migrations with the native setup use the `php artisan migrate` command 

### Docker
To run migrations with the docker setup use `docker exec [container name] php artisan migrate` or `./vendor/bin/sail artisan migrate` command if `laravel/sail` installed and being used.

# Portal Integration
To integrate an instance of [Symbiota](https://github.com/Symbiota/Symbiota) requires the following steps:

First create an empty folder in the root directory called `Portal`. This folder be named anything as long as it doesn't conflict with an existing folder in the root directory and matches the `PORTAL_NAME` variable found in the `.env`. `PORTAL_NAME` is set to `Portal` by default so if you changed it make sure the names match.

There are some problem currently with relative file paths `include`, javascript imports, and style sheet imports. If you run into any open a pr in [Symbiota](https://github.com/Symbiota/Symbiota) going into `development` branch.

After than setup portal as normal and make sure the enviroment variables for the database are configured to be the same in the parent and child portal.
