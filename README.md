Laravel Sinput
==========

Sinput was created to provide simple, familiar Laravel-like methods to obtain and sanitize user input of XSS and correct malformed HTML using very simple to very complex rules. Sinput utilizes the established and well supported [MeWebStudio/Purifier](https://github.com/mewebstudio/Purifier "MeWebStudio/Purifier"), a [Laravel](https://laravel.com/docs/5.7/ "Laravel") friendly implementation of [HTML Purifier](http://htmlpurifier.org/ "HTML Purifier").

### Compatibility
- Laravel 5.0 - 5.8
- PHP >= 5.5.9

### Installation

** **THIS PACKAGE IS NOT YET PUBLISHED PENDING TESTS** **

Install via composer.
`composer require devayes/sinput`

Add to `providers` in your config/app.php (Laravel 5.0 - 5.4 only, 5.6+ will auto-discover)
```php
    'providers' => [
        // ...
        Devayes\Sinput\SinputServiceProvider::class,
    ]
```
Add to `aliases` in your `config/app.php`.

```php
    'aliases' => [
        // ...
        'Sinput' => Devayes\Sinput\Facades\Sinput::class,
    ]
```
### Configuration
You need to publish the Mews\Purifier configuration to configure your own HTML sanitization rules.

`$ php artisan vendor:publish --provider="Mews\Purifier\PurifierServiceProvider"`

A file named `purifier.php` will appear in your `config` directory. You'll notice in the configuration file that the `'default'` setting allows a standard set of permissible HTML. I prefer stripping **all** HTML by default using this configuration:
```php
    'default' => [
        'HTML.Doctype' => 'HTML 4.01 Transitional',
        'Core.Encoding' => 'UTF-8',
        'HTML.Allowed' => '',
    ],
    'html' => [
        'HTML.Doctype'             => 'HTML 4.01 Transitional',
        'HTML.Allowed'             => 'div,b,strong,i,em,u,a[href|title],ul,ol,li,p[style],br,span[style],img[width|height|alt|src]',
        'CSS.AllowedProperties'    => 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align',
        'AutoFormat.AutoParagraph' => false,
        'AutoFormat.RemoveEmpty'   => true,
   ],
 ```

Sinput decodes HTML entities by default before sanitizing, there are options available to prevent that. These options can be set in code at run-time and can also be over-ridden by publishing and editing the configuration file. 

`$ php artisan vendor:publish --provider="Devayes\Sinput\SinputServiceProvider"`

It's recommended you read the description of the options and test various input and tune to your preference. By default, `decode_input` is set to `true` so that all input is decoded and the rules are applied. `decode_output` also defaults to `true` to prevent entities from being double encoded using Laravel's blade encoding. 

### Methods
- **I'll be using the above sample configurations in the examples below.**

##### Procedural function: 
* Strip all HTML in a variable or an array. Optionally provide a default value if the key is missing from input. 
```php
$var = '<b>bold</b>';
echo sinput($var, 'Default value'); // bold
```

* Allow HTML defined in `'html'` portion of config above.
```php
$var = '<b>bold</b>';
echo sinput($var, 'Default value', 'html'); // <b>bold</b>
```

##### Psuedo-static methods:
- **If no config option is provided, the default (as seen in the above example) will be used.**

##### Settings over-rides:
* Decode HTML entities before filtering (default: true)
```php
Sinput::setDecodeInput( bool $decode_input = true )
```

* Decode HTML entities after filtering (default: true)
```php
Sinput::setDecodeOutput( bool $decode_output = true )
```

##### Utility methods:
* Get all input and apply default config options.  
```php
// ?foo=<b>bar</b>&cow=<p>moo</p>
Sinput::all(); // strip all html. eg: foo => bar, cow => moo
Sinput::all('html'); // allow html specified in config above. eg: foo => <b>bar</b>, , cow => <p>moo</p>
```

* Get an item from the request   
```php
// ?foo=<b>bar</b>&cow=<p>moo</p>
Sinput::get('foo', 'Default value'); // strip all html. eg: foo => bar
Sinput::get('foo', 'Default value', 'html); // allow html. eg: foo => <b>bar</b>
```

* Get items from the request by keys.
```php
// ?foo=<b>bar</b>&cow=<p>moo</p>
Sinput::only('foo'); // strip all html. eg: foo => bar
Sinput::only(['foo', 'cow']); // strip all html. eg: foo => bar, cow => moo
Sinput::only('cow', 'html'); // allow html. eg: cow => <p>moo</p>
```

* Get all items *except* those specified.  
```php
// ?foo=<b>bar</b>&cow=<p>moo</p>&woo=<i>wee</i>
Sinput::except('foo'); // strip all html. eg: cow => moo, woo => wee 
Sinput::except(['foo', 'cow']); // strip all html. eg: woo => wee
Sinput::except('foo', 'html'); // allow html. eg: cow => <p>moo</p>, woo => <i>wee</i>
```

* Similar to Laravel's `$request->old()` method, but able to scrub HTML or apply config rules.
```php
// 'old' => ['foo' => '<b>bar</b>', 'cow' => '<p>moo</p>']
Sinput::old('foo', 'Default value'); // strip all html. eg: foo => bar
Sinput::old('foo', 'Default value', 'html); // allow html. eg: foo => <b>bar</b>
```

* Return items from request in variables.  
```php
// ?foo=<b>bar</b>&cow=<p>moo</p>
list($foo, $cow) = Sinput::list(['foo', 'cow']); // strip all html. eg: $foo = 'bar';
list($foo, $cow) = Sinput::list(['foo', 'cow'], 'html'); // allow html. eg: $foo = '<b>bar</b>';
```
or 
```php
// ?foo=<b>bar</b>&cow=<p>moo</p>
list($foo) = Sinput::list('foo'); // $foo = 'bar';
```

* Match request keys using regex.  
```php
// ?foo=<b>bar</b>&cow=<p>moo</p>&woo=<i>wee</i>
Sinput::match("#^[f|w]#"); // strip all html. eg: foo => bar, woo => wee
Sinput::match("#^[f|w]#", 'html'); // allow html. eg: foo => <b>bar</b>, woo => <i>wee</i>
```

### For more information on configurations for the underlying packages, please see:
- [HTML Purifier](http://htmlpurifier.org/ "HTML Purifier")
- [MeWebstudio/Purifier](https://github.com/mewebstudio/Purifier "MeWebstudio/Purifier")
