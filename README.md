
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

Sinput (Secure Input) XSS & HTML filtering package for Laravel Framework
==========

Sinput (a concatenation of "Secure Input") was created to provide simple, familiar methods to scrub user input of XSS and filter unwanted HTML, while also correcting malformed HTML using HTMLPurifier rulesets. Sinput is built on top of the venerable, established, and well supported [HTML Purifier](http://htmlpurifier.org/ "HTML Purifier").


### Compatibility
- Laravel 7, 8+

## Installation

Install via composer.
```bash
$ composer require devayes/sinput:^2.0
```

## Configuration
Publish the configuration file via:
```bash
$ php artisan vendor:publish --provider="Devayes\Sinput\SinputServiceProvider"
```

A file named `sinput.php` will appear in your `config` directory. You'll notice in the `purifier` section of that file that the `'default'` setting allows no HTML. There is also an `html` ruleset that allows a much more permissible set of tags and properties. You can add and remove rulesets to suit your needs, mind the `default_ruleset` value in the config as it will be applied when no ruleset is passed into the request method or helper function.

If you want to use the middleware (documented below) to sanitize all incoming request data, set the `middleware_ruleset` to your preference. You can use this to strip all HTML/XSS or allow the maximum amount of HTML permitted by your application.

By default, `decode_input` is set to `true` so that all input is decoded and the rules are applied. `decode_output` also defaults to `true` to prevent entities from being double encoded when using Laravel's blade encoding.

## How To

**Note:** I'll be using the default configurations in the examples below.

### Helper Function:
** Strip all html per the default ruleset in the config.**
```php
$array = ['foo' => '<b>bold</b>', 'cow' => 'moo'];
echo scrub($array); // Prints: [foo => bold, cow => moo]
```
** Allow html per the `html` ruleset in the config.**
```php
$var = '<b>bold</b>';
echo scrub($var, 'html'); // Prints: <b>bold</b>
```

### Request Method:
**Strip all HTML in a request.**
```php
// ?foo=<b>bar</b>&cow=<p>moo</p>
echo $request->sinput()->only('foo'); // Prints: [foo => bar]
```

**Allow HTML defined in `'html'` ruleset option in the `config/sinput.php` config.**
```php
// ?foo=<b>bar</b>&cow=<p>moo</p>
echo $request->sinput('html')->input('foo'); // Prints: <b>bar</b>
```

### Middleware
**NOTE:** Set your preferred ruleset in the `middleware_ruleset` option in `config/sinput.php`.

To apply the middleware filter to routes, add it to the `$routeMiddleware` array:
```php
protected $routeMiddleware = [
    //...
    'sinput' => \Devayes\Sinput\Middleware\SinputMiddleware::class,
    //...
];
```
And then in your routes, you can specify the ruleset. If no ruleset is specified, the `middleware_ruleset` in `config/sinput.php` will be used. See: [Laravel Middleware](https://laravel.com/docs/8.x/middleware) & [Laravel Route](https://laravel.com/docs/8.x/routing) Documentation for more info.
```php
Route::post('/article/save', ['middleware' => 'sinput', 'uses' => 'ArticlesController@postSave']); // Strips HTML per the middleware_ruleset in the config
Route::post('/article/save', ['middleware' => 'sinput:html', 'uses' => 'ArticlesController@postSave']); // Applies the html ruleset, allowing HTML
```

### Macros
Use macros to add your own custom methods.
```php
\Devayes\Sinput\Sinput::macro('nl2br', function ($value) {
    return nl2br(scrub($value));
});

$var = "<b>Line one</b>\rLine 2";
echo sinput()->nl2br($var); // Prints: Line one<br>Line 2
echo sinput('html')->nl2br($var); // Prints: <b>Line one</b><br>Line 2
```

## To learn more about configuration options for HTMLPurifier package, please see:
- [HTML Purifier](http://htmlpurifier.org/live/configdoc/plain.html "HTML Purifier")
