# Table of Contents  
* [Database](#database)  
    * [Configuration](#configuration)  
    * [Migrations](#migrations)  
* [New Tables](#new-tables)  
    * [Users](#users_table)  

# Database 
This is an overview of how Symbiota-Laravel database is configured. We will be loosely following information in the [Laravel Docs](https://laravel.com/docs/11.x/database) but will only be highlighting the parts relevant Symbiota-Laravel.

## Configuration
All database configuration is handled in the `config/database.php` file which returns an array with the below values.
```php
return [
     // What connection to use
    'default' => env('DB_CONNECTION', 'mysql'),
     // Connection information
    'connections' => [
        ...,
        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ]
    ],
     // Name of the Migrations Table
    'migrations' => 'migrations',
];
```
The `env(...)` function pulls values as defined in the `.env` file. Subsequent arguments will be used as the fallback if nothing is defined.

Here is an example of what the `.env` defintions would look like
```.env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=user
DB_PASSWORD=password
```

## Migrations
Migrations are simply a record of database changes and we use them to keep the database schema identical across developer and production environments. This is a built in feature with laravel and serves to replace the need to manage sql patch scripts independently.

At the bottom of the database config above there is a key called `migrations` which refers to the table in which all of the migrations are stored. It is a pretty simple table that looks like this.

```sql
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

The migrations table is itself a migration that is stored within the default setup of a laravel application and to get the table populated you must run the following to run all migrations:

```
php artisan migrate
```

Which results in the migrations table being created the all the migrations in the `database/migrations/` directory to be run. Note this can fail if their is conficting tables or constraints just like normal sql patches.

```
+----+-------------------------------------------------------+-------+
| id | migration                                             | batch |
+----+-------------------------------------------------------+-------+
|  1 | 2014_10_12_000000_create_users_table                  |     1 |
|  2 | 2014_10_12_100000_create_password_reset_tokens_table  |     1 |
|  3 | 2019_08_19_000000_create_failed_jobs_table            |     1 |
|  4 | 2019_12_14_000001_create_personal_access_tokens_table |     1 |
+----+-------------------------------------------------------+-------+
```
I will go over the above tables in more detail below but for now they are just utility tables created for some out of the box features laravel. Something to note here is that `batch` value. Since we ran all of these migrations at once they all get the same batch number. 

### Users Table
Default users table that laravel ships with looks like this.

```sql
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `laravel_users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```
Now this would be really helpful if the Symbiota was being written from scratch however there is already a users table in Symbiota and so things get a little bit more complicated. This is because the authentication system in laravel assumes that the user table looks like thisand has prebuilt functionality around it which would be nice to use.

In order to do this we just need to port over the old user table to the new one in a migration and then use the old password to generate a new password hash when the user logins or over email recover when updating to the new system.
