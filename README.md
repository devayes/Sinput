
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

A file named `sinput.php` will appear in your `config` directory. You'll notice in the `purifier` section of that file that the `'no_html'` setting allows **no HTML**. There is also an `allow_html` ruleset that allows a much more permissible set of HTML tags and properties. You can add and remove rulesets to suit your needs, mind the default values in the config, as they are used when no ruleset is specified in request method or helper calls.

If you want to use the middleware (documented below) to sanitize all incoming request data, set the `middleware_ruleset` to your preference. You can use this to strip all HTML/XSS via the `no_html` ruleset or allow the maximum amount of HTML permitted by your application (example provided in the `allow_html` ruleset).

By default, `decode_input` is set to `true` so that all input is decoded and the rules are applied. `decode_output` also defaults to `true` to prevent entities from being double encoded when using Laravel's blade encoding.

## How To

**Note:** I'll be using the default configurations in `config/sinput.php` for the examples below.

### Helper Function:
**Strip all html per the default ruleset in the config.**
```php
$array = ['foo' => '<b>bold</b>', 'cow' => 'moo'];
echo scrub($array); // Prints: [foo => bold, cow => moo]
echo scrub($array, 'no_html'); // Prints: [foo => bold, cow => moo]
// Same as sinput()->clean($array);
```
**Allow html per the `allow_html` ruleset in the config.**
```php
$var = '<b>bold</b>';
echo scrub($var, 'allow_html'); // Prints: <b>bold</b>
```
**Filter request input or retrieve the sinput object**
```php
// Retrieve an item from get/post
$foo = sinput('index', 'default value', $ruleset);
$sinput_obect = sinput();
$foo = $sinput_obect->input('index', 'default value', $ruleset);
$all = $sinput_obect->all(['foo', 'bar'], $ruleset);
$post = $sinput_obect->post('index', 'default value', $ruleset);
// Also supported: query, only, except
```

### Request Macro
**The `scrub` and `sinput` request macros make it very easy to apply multiple rulesets to multiple inputs while maintaining chainablity with all other request methods.**
```php
// Applies the `default_ruleset` option (`allow_html`) to all input.
$request->scrub()->all();
// Allow/repair html for `title` and `description` inputs by applying the `allow_html` ruleset option.
$request->scrub(['title', 'description'], 'allow_html')->all();
// Apply a custom ruleset config `titles` to 'title' and 'subtitle' inputs.
$request->scrub(['title', 'subtitle'], 'titles')->all();
// Strip html in the `title` input, allow html in `description` input.
$request->->scrub('title', 'no_html')->scrub('description', 'allow_html')->all();
// Retrieve items from input:
$title = $request->sinput('title', 'default value', $ruleset); // Not a chainable method.
// Using with validation
$validated = $request->scrub(['title', 'description'], 'no_html')->validate([
    'title' => 'required|unique:posts|max:255',
    'description' => 'required'
]);
```

**Use macros to add your own custom methods.**
```php
\Devayes\Sinput\Sinput::macro('nl2br', function ($value, $ruleset) {
    return nl2br(scrub($value, $ruleset));
});

$var = "<b>Line one</b>\rLine 2";
echo sinput()->nl2br($var); // Prints: Line one<br>Line 2
echo sinput()->nl2br($var, 'allow_html'); // Prints: <b>Line one</b><br>Line 2
```

### Middleware
**NOTE:** Set your preferred ruleset in the `middleware_ruleset` option in `config/sinput.php`.

**To apply the middleware filter to routes, add it to the `$routeMiddleware` array:**
```php
protected $routeMiddleware = [
    //...
    'sinput' => \Devayes\Sinput\Middleware\SinputMiddleware::class,
    //...
];
```
**Then in your routes, you can specify the ruleset. If no ruleset is specified, the `middleware_ruleset` in `config/sinput.php` will be used. See: [Laravel Middleware](https://laravel.com/docs/8.x/middleware) & [Laravel Route](https://laravel.com/docs/8.x/routing) Documentation for more info.**
```php
// Use: sinput:$ruleset,$input1|$input2|$input3
// Apply default middleware ruleset from config to all input
Route::post('/article/save', ['middleware' => 'sinput', 'uses' => 'ArticlesController@postSave']);
// Strip html from title & description input
Route::post('/article/save', ['middleware' => 'sinput:no_html,title|description', 'uses' => 'ArticlesController@postSave']); // Allow HTML specified in alow_html ruleset for all input
Route::post('/article/save', ['middleware' => 'sinput:allow_html', 'uses' => 'ArticlesController@postSave']);
```

### Please Note:
**Be careful not to apply filtering rules on a file input. As it is likely to exhaust memory.**

## To learn more about configuration options for the HTMLPurifier package, please see:
- [HTML Purifier](http://htmlpurifier.org/live/configdoc/plain.html "HTML Purifier")
