


Laravel Sinput
==========

Sinput was created to provide simple, familiar Laravel-like methods to scrub user input of HTML and/or XSS while correcting malformed HTML using very simple to very complex rules. Sinput utilizes the established and well supported [HTML Purifier](http://htmlpurifier.org/ "HTML Purifier").

### Use case
Sinput is an adaptation of HtmlPurifier's intelligent and unbeatable XSS scrubbing and HTML rule based filtering and repair of malformed HTML. I've been using this adaptation for years to filter request input and scrub HTML (even encoded html) from input fields where it isn't allowed and applying strict rules for HTML in other input fields where it is allowed. By specifying a configuration option in the methods below you can apply a very specific set of rules depending on your needs.

### Compatibility
- Laravel 5.0 - 5.8
- PHP >= 5.5.9

## Installation

Install via composer.
`composer require devayes/sinput:1.0`

**Laravel < 5.4** Add to `providers` in your config/app.php
```php
    'providers' => [
        // ...
        Devayes\Sinput\SinputServiceProvider::class,
    ]
```
*Optionally*, add the facade to `aliases` in your `config/app.php`. Otherwise, you can use the helper function documented below instead.

```php
    'aliases' => [
        // ...
        'Sinput' => Devayes\Sinput\Facades\Sinput::class,
    ]
```
## Configuration
Publish the configuration file via:
`$ php artisan vendor:publish --provider="Devayes\Sinput\SinputServiceProvider"`

A file named `sinput.php` will appear in your `config` directory. You'll notice in the `purifier` section of that file that the `'default'` setting allows no HTML. There is also an `html` ruleset that allows a much more permissible set of tags and properties. You can add and remove rulesets to suit your needs, but mind the `default_ruleset` value set higher in the config as it will be applied when no ruleset is passed into the facade or helper function.

Sinput decodes HTML entities by default before sanitizing, there are options available to prevent that. These options can also be set in code at run-time.

By default, `decode_input` is set to `true` so that all input is decoded and the rules are applied. `decode_output` also defaults to `true` to prevent entities from being double encoded when using Laravel's blade encoding.

If you want to use the middleware (documented below) to sanitize all incoming request data, set the `middleware_ruleset` to your preference.

## Methods
**I'll be using the default configurations in the examples below.**

### Helper function:
**Strip all HTML in a request. Optionally provide a default value if the key is missing from input.**
```php
// ?foo=<b>bar</b>&cow=<p>moo</p>
echo sinput('foo', 'Default value'); // bar
```

**Allow HTML defined in `'html'` portion of config above.**
```php
// ?foo=<b>bar</b>&cow=<p>moo</p>
echo sinput('foo', 'Default value', 'html'); // <b>bar</b>
```

### Run-time configuraftion over-rides:
**Decode HTML entities before filtering (default: true)**
```php
sinput()->setConfig('decode_input', true);
```

**Decode HTML entities after filtering (default: true)**
```php
sinput()->setConfig('decode_output', true);
```

**Over-ride default rule set (default: 'default')**
```php
sinput()->setConfig('default_ruleset', 'html');
```


### Utility methods:
**Get all input and optionally apply config rulesets.**
```php
// ?foo=<b>bar</b>&cow=<p>moo</p>
sinput()->all(); // [foo => bar, cow => moo]
- or -
Sinput::all(); // strip all html. eg: [foo => bar, cow => moo]
Sinput::all('html'); // allow html specified in config above. eg: [foo => <b>bar</b>, cow => <p>moo</p>]
```

**Scrub a variable or an array.**
```php
$foo = '<b>bar</b>';
sinput()->clean($foo); // bar
sinput()->clean($foo, 'Default value', 'html'); // <b>bar</b>
$foo = ['bar' => '<b>baz</b>'];
sinput()->clean($foo); // ['bar' => 'baz']
```

**Get an item from the request.**
```php
// ?foo=<b>bar</b>&cow=<p>moo</p>
sinput()->get('foo', 'Default value', 'html'); // <b>bar</b>
- or -
Sinput::get('foo', 'Default value'); // strip all html. eg: bar
Sinput::get('foo', 'Default value', 'html'); // allow html. eg: <b>bar</b>
```

**Get items from the request by keys.**
```php
// ?foo=<b>bar</b>&cow=<p>moo</p>
sinput()->only('foo'); // strip all html. eg: [foo => bar]
- or -
Sinput::only('foo'); // strip all html. eg: bar
Sinput::only(['foo', 'cow']); // strip all html. eg: [foo => bar, cow => moo]
Sinput::only('cow', 'html'); // allow html. eg: [cow => <p>moo</p>]
```

**Get all items *except* those specified.**
```php
// ?foo=<b>bar</b>&cow=<p>moo</p>&woo=<i>wee</i>
sinput()->except('foo', 'html'); // allow html. eg: [cow => <p>moo</p>, woo => <i>wee</i>]
- or -
Sinput::except('foo'); // strip all html. eg: [cow => moo, woo => wee]
Sinput::except(['foo', 'cow']); // strip all html. eg: [woo => wee]
Sinput::except('foo', 'html'); // allow html. eg: [cow => <p>moo</p>, woo => <i>wee</i>]
```

**Similar to Laravel's `$request->old()` method, but able to scrub HTML or apply other html filtering rules.**
```php
// 'old' => ['foo' => '<b>bar</b>', 'cow' => '<p>moo</p>']
sinput()->old('foo', 'Default value', 'html'); // allow html. eg: [foo => <b>bar</b>]
- or -
Sinput::old('foo', 'Default value'); // strip all html. eg: [foo => bar]
Sinput::old('foo', 'Default value', 'html'); // allow html. eg: [foo => <b>bar</b>]
```

**Return items from request in variables.**
```php
// ?foo=<b>bar</b>&cow=<p>moo</p>
list($foo) = sinput()->list('foo'); // $foo = 'bar';
- or -
list($foo, $cow) = Sinput::list(['foo', 'cow']); // strip all html. eg: $foo = 'bar'; $cow = 'moo';
list($foo, $cow) = Sinput::list(['foo', 'cow'], 'html'); // allow html. eg: $foo = '<b>bar</b>'; $cow = '<p>moo</p>'
- or -
list($foo) = Sinput::list('foo'); // $foo = 'bar';
```

**Match request keys using regex.**
```php
// ?foo=<b>bar</b>&cow=<p>moo</p>&woo=<i>wee</i>
sinput()->match("#^[f|w]#", 'html'); // allow html. eg: [foo => <b>bar</b>, woo => <i>wee</i>]
- or -
Sinput::match("#^[f|w]#"); // strip all html. eg: [foo => bar, woo => wee]
Sinput::match("#^[f|w]#", 'html'); // allow html. eg: [foo => <b>bar</b>, woo => <i>wee</i>]
```

## Middleware
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
**NOTE:** Make sure you've configured the `middleware_ruleset` in `config/sinput.php`.

## Extending Sinput
If you need a method that doesn't exist or need access to more advanced features of HTMLPurifier, you can extend the Sinput class and utilize all of the parent methods (or submit a pull request).
```php
class MyClass extends Devayes\Sinput\Sinput {

    public function foo($value, $default = null, $config = null)
    {
        return $this->clean($value, $default, $config);
    }
}
```

## Run tests:
- `$ cd vendor/devayes/sinput`
- `$ composer install`
- `$ phpunit --verbose`

## For more information on configurations for the underlying package, please see:
- [HTML Purifier](http://htmlpurifier.org/ "HTML Purifier")

## Thanks for the inspiration:
- [MeWebstudio/Purifier](https://github.com/mewebstudio/Purifier "MeWebstudio/Purifier")
- [Graham Campbell - Binput](https://github.com/GrahamCampbell/Laravel-Binput "Graham Campbell/Binput")
