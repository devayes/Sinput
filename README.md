
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

Laravel Sinput
==========

Sinput (a concatenation of "Secure Input") was created to provide simple, familiar, Laravel-like methods to scrub user input of XSS and unwanted HTML, while correcting malformed HTML using very simple, to very complex rules. Sinput utilizes the respected, established, and well supported [HTML Purifier](http://htmlpurifier.org/ "HTML Purifier").


### Compatibility
- Laravel & Lumen 6, 7, 8+

## Installation

Install via composer.
```bash
$ composer require devayes/sinput
```

**Laravel Framework**

*Optionally*, add the facade to `aliases` in your `config/app.php`. Otherwise, you can extend the SinputAbstract class or use the helper function documented below instead.

```php
    'aliases' => [
        // ...
        'Sinput' => Devayes\Sinput\Facades\Sinput::class,
    ]
```

**Lumen Framework**

In `bootstrap/app.php` in the "Register Service Providers" section, add:
```php
$app->register(Devayes\Sinput\SinputServiceProvider::class);
```
If you want to use the facade, you'll also need to uncomment this line in `bootstrap/app.php`:
```php
$app->withFacades();
```

## Configuration
Publish the configuration file via:
```bash
$ php artisan vendor:publish --provider="Devayes\Sinput\SinputServiceProvider"
```

A file named `sinput.php` will appear in your `config` directory. You'll notice in the `purifier` section of that file that the `'default'` setting allows no HTML. There is also an `html` ruleset that allows a much more permissible set of tags and properties. You can add and remove rulesets to suit your needs, mind the `default_ruleset` value in the config as it will be applied when no ruleset is passed into the facade or helper function.

If you want to use the middleware (documented below) to sanitize all incoming request data, set the `middleware_ruleset` to your preference. You can use this to strip all HTML/XSS or allow the maximum amount of HTML permitted by your application.

By default, `decode_input` is set to `true` so that all input is decoded and the rules are applied. `decode_output` also defaults to `true` to prevent entities from being double encoded when using Laravel's blade encoding.

## Methods

**Note:** I'll be using the default configurations in the examples below.

### Helper function:
**Strip all HTML in a request. Optionally provide a default value if the key is missing from the request.**
```php
// ?foo=<b>bar</b>&cow=<p>moo</p>
echo sinput('doesnt_exist', 'Default value'); // Default Value
echo sinput('foo'); // bar
$sinput = sinput(); // Sinput object. EG: sinput()->query('foo', null, 'html'); // <b>bar</b>
```

**Allow HTML defined in `'html'` portion of the config.**
```php
// ?foo=<b>bar</b>&cow=<p>moo</p>
echo sinput('foo', 'Default value', 'html'); // <b>bar</b>
```

### Configuration methods:
```php
$decode = sinput()->getConfig('decode_input'); // true
sinput()->setConfig('decode_input', true);
```
Use the normal dot notation to get or set configuration options.

### Utility methods:
**Get all input and optionally apply a config ruleset.**
```php
// ?foo=<b>bar</b>&cow=<p>moo</p>
sinput()->all(); // [foo => bar, cow => moo]
sinput()->all('html'); // allow html specified in config above. eg: [foo => <b>bar</b>, cow => <p>moo</p>]
- or -
Sinput::all(); // strip all html. eg: [foo => bar, cow => moo]
Sinput::all('html'); // allow html. eg: [foo => <b>bar</b>, cow => <p>moo</p>]
```

**Scrub a variable or an array.**
```php
$foo = '<b>bar</b>';
sinput()->clean($foo); // bar
sinput()->clean($foo, 'Default value', 'html'); // <b>bar</b>
$foo = ['bar' => '<b>baz</b>'];
sinput()->clean($foo); // ['bar' => 'baz']
- or -
Sinput::clean($foo); // bar
Sinput::clean($foo, 'Default value', 'html'); // <b>bar</b>
```

**Get an item from the request (get, post).**
```php
// ?foo=<b>bar</b>&cow=<p>moo</p>
sinput()->input('foo'); // bar
sinput()->input('foo', 'Default value', 'html'); // <b>bar</b>
- or -
Sinput::input('foo', 'Default value'); // strip all html. eg: bar
Sinput::input('foo', 'Default value', 'html'); // allow html. eg: <b>bar</b>
```

**Get an item from $_GET.**
```php
// ?foo=<b>bar</b>&cow=<p>moo</p>
sinput()->query('foo'); // bar
sinput()->query('foo', 'Default value', 'html'); // <b>bar</b>
- or -
Sinput::query('foo', 'Default value'); // strip all html. eg: bar
Sinput::query('foo', 'Default value', 'html'); // allow html. eg: <b>bar</b>
```

**Allow HTML defined in `'html'` ruleset option in the `config/sinput.php` config.**
```php
// ?foo=<b>bar</b>&cow=<p>moo</p>
echo $request->scrub('allow_html')->input('foo'); // Prints: <b>bar</b>
echo $request->scrub('allow_html')->all(); // Prints: [foo => <b>bar</b>, cow => <p>moo</p>]
```

## Middleware
**NOTE:** Make sure you've configured the `middleware_ruleset` in `config/sinput.php`.

To apply the middleware filter to routes, add it to the `$routeMiddleware` array:
```php
protected $routeMiddleware = [
    //...
    'sinput' => \Devayes\Sinput\Middleware\SinputMiddleware::class,,
    //...
];
```
And then in your routes, you can specify the ruleset, if no ruleset is specified it'll default to the `middleware_ruleset` in `config/sinput.php`.
```php
Route::post('/article/save', ['middleware' => 'sinput', 'uses' => 'ArticlesController@postSave']); // Strips HTML per the middleware_ruleset in the config
Route::post('/article/save', ['middleware' => 'sinput:allow_html', 'uses' => 'ArticlesController@postSave']); // Applies the html ruleset, allowing HTML
```

To filter *all* request input, add the middleware to `app/Http/Kernel.php` in the `$middlewareGroups` `web` array:
```php
protected $middlewareGroups = [
        'web' => [
            //...
            \Devayes\Sinput\Middleware\SinputMiddleware::class,
            //...
        ],
        //...
];
```


## Blade directive (Laravel only)

```php
$var = '<b>bold</b>';
@sinput($var) // bold
@sinput($var, 'html') // <b>bold</b>
$arr = ['foo' => '<b>bar</b>'];
@sinput($var) // ['foo' => 'bar']
@sinput($var, 'html') // ['foo' => '<b>bar</b>']

```

## To learn more about configuration options for HTMLPurifier package, please see:
- [HTML Purifier](http://htmlpurifier.org/ "HTML Purifier")
