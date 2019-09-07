# Laravel Translatable
![](https://img.shields.io/github/v/release/sertxudeveloper/laravel-translatable) ![](https://img.shields.io/github/license/sertxudeveloper/laravel-translatable) ![](https://img.shields.io/librariesio/github/sertxudeveloper/laravel-translatable) ![](https://img.shields.io/github/repo-size/sertxudeveloper/laravel-translatable) ![](https://img.shields.io/packagist/dt/sertxudeveloper/laravel-translatable) ![](https://img.shields.io/github/issues/sertxudeveloper/laravel-translatable) ![](https://img.shields.io/packagist/php-v/sertxudeveloper/laravel-translatable)

**Manage localized routes and use translatable models in a Laravel app.**
 - Automatically register the routes for each locale you wish to support.
 - Optionally remove the locale slug from the URL for your main language.
 - Generate localized route URL's using the ``trans_route()`` helper.
 - Allow routes to be cached.

## Requirements
  - PHP >= 7.1
  - Laravel >= 5.6

## Installation
You can install this package using Composer.

```sh
composer require sertxudeveloper/laravel-translatable
```

## Configuration
First you should publish the configuration file.

```sh
php artisan vendor:publish --provider="SertxuDeveloper\Translatable\TranslatableServiceProvider" --tag="config"
```

After running this command, you will now find a ``translatable.php`` file in the ``config`` folder.

### Locales availables
You can set the locales availables in your application. The localized routes will be registered with all of these locales.

```php
"locales" => ["es", "en", "it"],
```
### Fallback locale
The fallback locale should be the one that not require to be translated.

```php
"fallback_locale" => "es",
```
You can also hide the fallback locale from the URL prefix.

```php
"hide_fallback_locale" => true,
```

# Routes
## Register Routes
All the routes you want lo localize should be registered inside the ``Route::localized`` closure.

```php
// Not Localized
Route::get('home', HomeController::class.'@index')->name('home');

// Localized
Route::localized(function () {
  Route::get('blog', BlogController::class.'@index')->name('blog')
  Route::get('{slug}', PageController::class.'@index')->name('page')
});

// Not Localized
Route::get('about', AboutController::class.'@index')->name('about');
```

Inside this closure you can use Route Groups such as Middlewares, Namespaces or even Sub-Domain Routing. This closure will prepend the locale to the route's URI and name.

This will be the result of the viewed configuration examples.

| URI | Name | Locale |
| --- | --- | --- |
| /home | home | es - en - it |
| --- | --- | --- |
| /blog | blog | es |
| /es/blog | es.blog | es |
| /en/blog | en.blog | en |
| /it/blog | it.blog | it |
| --- | --- | --- |
| /{slug} | page | es |
| /es/{slug} | es.page | es |
| /en/{slug} | en.page | en |
| /it/{slug} | it.page | it |
| --- | --- | --- |
| /about | about | es - en - it |

> Beware that you don't register the same URL twice when omitting the locale. You can't have a localized /about route and also register a non-localized /about route in this case. The same idea applies to the / (root) route! Also note that the route names still have the locale prefix.

## Generate Localized URLs
You should use the ``trans_route`` helper in order to get the requested route localized. To this helper you can pass a route name or a route url, in booth cases it will be localized.

```php
trans_route($name, $parameters = [], $absolute = false, $locale = null)
```

If you pass only the route it will be localized using the current locale (``'en'``).

```php
trans_route('blog') // /en/blog
```

You can also pass params to the helper.

```php
trans_route('page', ['help']) // /en/help
```

The third param is a boolean to make it absolute or not.

```php
trans_route('page', ['help'], true) // http://your_server_address/en/help
```

```php
trans_route('page', ['help'], false) // /en/help
```

The last param is used for specify the locale to use.

```php
trans_route('blog', [], false, 'it') // /it/blog
```

## Switch Locale
If your building a dropdown or similar with links to change the locale of the application, you should use the ``switch_to_locale`` helper.
```php
switch_to_locale('en') // Changes to 'en' locale without changing the route
```

# Eloquent translations

## Create translations tables
You can to customize the name of the translations tables.

```php
"table_sufix" => "_translations"
```

The usage of this value will be the following one. If you have the model ``Page`` with the trait ``HasTranslations`` and the model table is ``pages``. This package will lookup for the translations at the table ``page_translations``. Always the model table followed by the table suffix in the config file.

The translations tables should contain the translatable fields from the model, the id, a column ``locale`` to specify the language saved, ``created_at`` and ``updated_at``. The column ``deleted_at`` should **never** be in the translations table, regardless the models is ``softDeleted`` or not.

As you can see in the following example, 

#### Pages table
| id | name | slug | excerpt | body | image | status | created_at | updated_at | 
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| int | varchar | varchar | varchar | text | varchar | enum | datetime | datetime |

#### Pages translation table
| id | locale | name | excerpt | body | created_at | updated_at | 
| --- | --- | --- | --- | --- | --- | --- |
| int | varchar(2) | varchar | varchar | text | datetime | datetime |

## Get Eloquent Translated Attribute
In order to get a translated attribute you should use the ``getTranslated`` method.

```php
$post = Post::find(1);
echo $post->getTranslated('name');
```

## Cache Routes
In production you can safely cache your routes per usual.

```sh
php artisan route:cache
```
<br><br>
Copyright (c) 2019 Sertxu Developer
