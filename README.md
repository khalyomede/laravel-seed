# laravel-seed

Run your seeds like you run your migrations.

![laravel-seed-showcase-2](https://user-images.githubusercontent.com/15908747/88453817-cda1a700-ce6a-11ea-86b5-923d6aeb98ee.gif)

## Summary

- [About](#about)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Usage](#usage)

## About

I created this package because I am working with tables that are pre-filled with data (like a Gender table, a Product type table, and so on).

I found a series of package, laravel-seeder, which seemed to have been forked a lot of times, but either [the packages would not install properly](https://github.com/eighty8/laravel-seeder/issues/20), or the forks were really outdated.

I decided to take a fresh start and create this package from scratch, with having in mind to be as close as the official migration experience.

In this approach, each seeders is a class, like a migration: it defines both "up" and "down" methods, so you can run and rollback your seeds. Like migrations, the order of the files is determined by a timestamp at the creation time.

My use case for this package is to populate my apps in production, in order to automatize this process, without having to deal with running each seed class individually, and manually.

## Prerequisites

- PHP >= 7.0.0
- Laravel >= 5.5.0

## Installation

In your root project folder, run this command:

```bash
composer require khalyomede/laravel-seed
```

For older Laravel versions, you will need to register the service provider in the key "Providers" of the `config/app.php` file like following:

```php
'providers' => [
  // ...
  Khalyomede\LaravelSeed\LaravelSeedServiceProvider::class,
]
```

## Usage

- [Create a new seeder](#create-a-new-seeder)
  - [Using the artisan command](#using-the-artisan-command)
  - [Specifying an Eloquent model](#specifying-an-eloquent-model)
- [Checking the status of your seeds](#checking-the-status-of-your-seeds)
- [Running seeders](#running-seeders)
- [Rollbacking seeds](#rollbacking-seeds)
  - [Rollbacking everything](#rollbacking-everything)

### Create a new seeder

Like mentioned in the [about](#about) section, seeders are class-based. They define a up and down method, and are the only way to use the command lines to seed your database.

#### Using the artisan command

To create a new seeder, use this Artisan command line:

```php
php artisan seed:make InsertPostCategory
```

This will create a new seeder class at `database/seeders/2020_07_24_094613_insert_post_categories.php`. If you open the file, this is how it looks:

```php
class InsertPostCategories
{
  public function up()
  {
    //
  }

  public function down()
  {
    //
  }
}
```

#### Specifying an Eloquent model

In general seeders will populate a table modelized by one of your Eloquent model.

To specify through which Eloquent model your seeder will fill data, you can specify the `--model` argument like following:

```bash
php artisan seed:make InsertPostCategories --model=PostCategeory
```

The content of your seeder will be filled with the usual boilerplate code you would have written:

```php
use App\PostCategory;

class InsertPostCategories
{
  public function up()
  {
    PostCategory::insert([
      [
        "id" => 1,
      ],
    ]);
  }

  public function down()
  {
    PostCategory::destroy([
      1,
    ]);
  }
}
```

### Checking the status of your seeds

If you need to have a report of which seeds have been ran or not yet, you can use the following Artisan command:

```bash
php artisan seed:status
```

This will prompt a table, with each lines specifying the status of the seeder.

```bash
$ php artisan seed:status

+------------------------------------------+---------+
| file                                     | status  |
+------------------------------------------+---------+
| 2020_07_24_094613_insert_post_categories | not ran |
+------------------------------------------+---------+

1 row(s) displayed.
```

### Running seeders

When your seeders is ready, you can use this command to run any seeders that have not been ran yet.

```bash
php artisan seed
```

This will also prompt a table summarizing which seeders have been successfuly run.

```bash
$ php artisan seed

 1/1 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%

+------------------------------------------+
| file                                     |
+------------------------------------------+
| 2020_07_24_094613_insert_post_categories |
+------------------------------------------+

1 seed(s) ran.
```

### Rollbacking seeds

Rollbacking seeds can be useful to test again that everything goes right. There is two way to rollback seeds.

#### Rollbacking everything

If you are sure you need to remove every seeds, you can use the following command:

```bash
php artisan seed:reset
```

This will take every seeds in reverse order, and run their `up()` method.

```bash
$ php artisan seed:reset

 1/1 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%

+------------------------------------------+
| file                                     |
+------------------------------------------+
| 2020_07_24_094613_insert_post_categories |
+------------------------------------------+

1 seed(s) rollbacked.
```

### Rollbacking the last batch

When you run `php artisan seed`, it associate a "batch" number to the seeds that have been run at this time.

Let's imagine you are creating this blog post web app. You need to fill some post categories, so you run `php artisan seed` one time.

This package will associate the batch number "1" to this first seed batch.

Then, you need to modelize author genders, and author positions (junior or senior). So you create both the `Gender` and `Position` models, the seeders, and run a second time `php artisan seed`.

This will then associate the batch number "2" for these two additionals seeds.

At this point, this is what we have stored so far:

```bash
+------------------------------------------+--------------+
| file                                     | batch number |
+------------------------------------------+--------------+
| 2020_07_24_094613_insert_post_categories |            1 |
| 2020_07_25_065903_insert_genders         |            2 |
| 2020_07_25_075926_insert_positions       |            2 |
+------------------------------------------+--------------+
```

So when you run `php artisan seed:rollback`, this will rollback both `insert_positions` and `insert_genders` in a row, since they are in the same batch.

```bash
$ php artisan seed:rollback

 2/2 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%

+------------------------------------+
| file                               |
+------------------------------------+
| 2020_07_25_065903_insert_genders   |
| 2020_07_25_075926_insert_positions |
+------------------------------------+

2 seed(s) rollbacked.
```

Then, if you run again `php artisan seed:rollback`, this will rollback the `insert_post_categories` seed alone.

```bash
$ php artisan seed:rollback

 1/1 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%

+-----------------------------------------+
| file                                    |
+-----------------------------------------+
| 2020_07_24_094613_insertpost_categories |
+-----------------------------------------+

1 seed(s) rollbacked.
```
