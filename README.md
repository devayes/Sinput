
Laravel Sinput
==========

Sinput was created to provide simple, familiar Laravel-like methods to scrub user input of HTML and/or XSS while correcting malformed HTML using very simple to very complex rules. Sinput utilizes the established and well supported [MeWebStudio/Purifier](https://github.com/mewebstudio/Purifier "MeWebStudio/Purifier"), a [Laravel](https://laravel.com/docs/5.7/ "Laravel") friendly implementation of [HTML Purifier](http://htmlpurifier.org/ "HTML Purifier").

### Use case
Sinput is an adaptation of HtmlPurifier's intelligent and unbeatable XSS scrubbing and HTML rule based filtering. I've been using this adaptation for years to filter request input and scrub HTML (even encoded html) from input fields where it isn't allowed and applying strict rules for HTML in other input fields where it is allowed. By specifying a configuration option in the methods below you can apply a very specific set of rules depending on your needs.

### Compatibility
- Laravel 5.0 - 5.8
- PHP >= 5.5.9

### Installation

Install via composer.
`composer require devayes/sinput`

**Laravel < 5.4** Add to `providers` in your config/app.php
```php
    'providers' => [
        // ...
        Devayes\Sinput\SinputServiceProvider::class,
    ]
```
Optionally, add to `aliases` in your `config/app.php`. Otherwise, you can use the helper function documented below instead.

```php
    'aliases' => [
        // ...
        'Sinput' => Devayes\Sinput\Facades\Sinput::class,
    ]
```
### Configuration
You need to publish the Mews\Purifier configuration to configure your own HTML sanitization rules.

`$ php artisan vendor:publish --provider="Mews\Purifier\PurifierServiceProvider"`

A file named `purifier.php` will appear in your `config` directory. You'll notice in the configuration file that the `'default'` setting allows a standard set of permissible HTML. I prefer stripping **all** HTML by default by changing the current default set to `html` and creating a new set called `default` which will remove all html:
```php
    'default' => [
        'HTML.Doctype' => 'HTML 4.01 Transitional',
        'Core.Encoding' => 'UTF-8',
        'HTML.Allowed' => '',
        'AutoFormat.AutoParagraph' => false,
    ],
    'html' => [
        'HTML.Doctype'             => 'HTML 4.01 Transitional',
        'HTML.Allowed'             => 'div,b,strong,i,em,u,a[href|title],ul,ol,li,p[style],br,span[style],img[width|height|alt|src]',
        'CSS.AllowedProperties'    => 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align',
        'AutoFormat.AutoParagraph' => false,
        'AutoFormat.RemoveEmpty'   => true,
   ],
 ```
 
Publish the Sinput config file and add your preferred rule set to the `default_rule` config option.

`$ php artisan vendor:publish --provider="Devayes\Sinput\SinputServiceProvider"`

Sinput decodes HTML entities by default before sanitizing, there are options available to prevent that. These options can be set in code at run-time.

By default, `decode_input` is set to `true` so that all input is decoded and the rules are applied. `decode_output` also defaults to `true` to prevent entities from being double encoded when using Laravel's blade encoding.

### Methods
- **I'll be using the above recommended configurations in the examples below.**

##### Helper function:
* Strip all HTML in a request. Optionally provide a default value if the key is missing from input.
```php
// ?foo=<b>bar</b>&cow=<p>moo</p>
echo sinput('foo', 'Default value'); // bar
```

* Allow HTML defined in `'html'` portion of config above.
```php
// ?foo=<b>bar</b>&cow=<p>moo</p>
echo sinput('foo', 'Default value', 'html'); // <b>bar</b>
```

##### Psuedo-static methods:
- **If no config option is provided, the default set (as seen in the above example) will be used.**

##### Settings over-rides:
* Decode HTML entities before filtering (default: true)
```php
sinput()->setDecodeInput( bool $decode_input = true )
```

* Decode HTML entities after filtering (default: true)
```php
sinput()->setDecodeOutput( bool $decode_output = true )
```

##### Utility methods:
* Get all input and apply default config options.
```php
// ?foo=<b>bar</b>&cow=<p>moo</p>
sinput()->all(); // [foo => bar, cow => moo]
- or -
Sinput::all(); // strip all html. eg: [foo => bar, cow => moo]
Sinput::all('html'); // allow html specified in config above. eg: [foo => <b>bar</b>, cow => <p>moo</p>]
```

* Strip all HTML in a variable (or array). 
```php
$foo = '<b>bar</b>';
sinput()->clean($foo); // bar
sinput()->clean($foo, 'Default value', 'html'); // <b>bar</b>
$foo = ['bar' => '<b>baz</b>'];
sinput()->clean($foo); // ['bar' => 'baz']
```

* Get an item from the request
```php
// ?foo=<b>bar</b>&cow=<p>moo</p>
sinput()->get('foo', 'Default value', 'html'); // <b>bar</b>
- or -
Sinput::get('foo', 'Default value'); // strip all html. eg: bar
Sinput::get('foo', 'Default value', 'html'); // allow html. eg: <b>bar</b>
```

* Get items from the request by keys.
```php
// ?foo=<b>bar</b>&cow=<p>moo</p>
sinput()->only('foo'); // strip all html. eg: [foo => bar]
- or -
Sinput::only('foo'); // strip all html. eg: bar
Sinput::only(['foo', 'cow']); // strip all html. eg: [foo => bar, cow => moo]
Sinput::only('cow', 'html'); // allow html. eg: [cow => <p>moo</p>]
```

* Get all items *except* those specified.
```php
// ?foo=<b>bar</b>&cow=<p>moo</p>&woo=<i>wee</i>
sinput()->except('foo', 'html'); // allow html. eg: [cow => <p>moo</p>, woo => <i>wee</i>]
- or -
Sinput::except('foo'); // strip all html. eg: [cow => moo, woo => wee]
Sinput::except(['foo', 'cow']); // strip all html. eg: [woo => wee]
Sinput::except('foo', 'html'); // allow html. eg: [cow => <p>moo</p>, woo => <i>wee</i>]
```

* Similar to Laravel's `$request->old()` method, but able to scrub HTML or apply config rules.
```php
// 'old' => ['foo' => '<b>bar</b>', 'cow' => '<p>moo</p>']
sinput()->old('foo', 'Default value', 'html'); // allow html. eg: [foo => <b>bar</b>]
- or -
Sinput::old('foo', 'Default value'); // strip all html. eg: [foo => bar]
Sinput::old('foo', 'Default value', 'html'); // allow html. eg: [foo => <b>bar</b>]
```

* Return items from request in variables.
```php
// ?foo=<b>bar</b>&cow=<p>moo</p>
list($foo) = sinput()->list('foo'); // $foo = 'bar';
- or -
list($foo, $cow) = Sinput::list(['foo', 'cow']); // strip all html. eg: $foo = 'bar'; $cow = 'moo';
list($foo, $cow) = Sinput::list(['foo', 'cow'], 'html'); // allow html. eg: $foo = '<b>bar</b>'; $cow = '<p>moo</p>'
- or -
list($foo) = Sinput::list('foo'); // $foo = 'bar';
```

* Match request keys using regex.
```php
// ?foo=<b>bar</b>&cow=<p>moo</p>&woo=<i>wee</i>
sinput()->match("#^[f|w]#", 'html'); // allow html. eg: [foo => <b>bar</b>, woo => <i>wee</i>]
- or -
Sinput::match("#^[f|w]#"); // strip all html. eg: [foo => bar, woo => wee]
Sinput::match("#^[f|w]#", 'html'); // allow html. eg: [foo => <b>bar</b>, woo => <i>wee</i>]
```

### For more information on configurations for the underlying packages, please see:
- [HTML Purifier](http://htmlpurifier.org/ "HTML Purifier")
- [MeWebstudio/Purifier](https://github.com/mewebstudio/Purifier "MeWebstudio/Purifier")

### Thanks also to, for the inspiration:
- [Graham Campbell - Binput](https://github.com/GrahamCampbell/Laravel-Binput)
