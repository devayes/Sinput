Laravel Sinput
==========

Sinput was created to provide simple, familiar Laravel-like methods to obtain and sanitize user input of XSS and correct malformed HTML using very simple to very complex rules. Sinput utilizes the established and well supported [MeWebStudio/Purifier](https://github.com/mewebstudio/Purifier "MeWebStudio/Purifier"), a [Laravel](https://laravel.com/docs/5.7/ "Laravel") friendly implementation of [HTML Purifier](http://htmlpurifier.org/ "HTML Purifier").

### Compatibility
- Laravel 5.0 - 5.7
- PHP >= 5.5.9

### Installation
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
`sinput($var, $default = null)`

* Allow HTML defined in `'html'` portion of config above.
`sinput($var, $default = null, 'html')`

##### Psuedo-static methods:
- **If no config option is provided, the default (as seen in the above example) will be used.**

##### Settings over-rides:
* Decode HTML entities before filtering (default: true)
`Sinput::setDecodeInput([true|false])`

* Decode HTML entities after filtering (default: true)
`Sinput::setDecodeOutput([true|false])`

##### Utility methods:
* Get all input and apply default config options.  
`Sinput::all($config = [null|'html'])`

* Get an item from the request   
`Sinput::get($key, $default = 'default value', $config = [null|'html'])`

* Get items from the request by keys.
`Sinput::only(['name', 'email', bio'], $config = [null|'html'])`

* Get all items *except* those specified.  
`Sinput::except(['_token'], $config = [null|'html'])`

* Similar to Laravel's `$request->old()` method, but able to scrub HTML or apply config rules.
`Sinput::old($key, $default = null, $config = [null|'html'])`

* Return items from request in variables.  
`list($foo, $bar) = Sinput::list(['foo', 'bar'], $config = [null|'html']);`
or 
`list($foo) = Sinput::list('foo');`

* Match request keys using regex.  
`Sinput::match($regex, $config = [null|'html'])`

### Thank you
- [HTML Purifier](http://htmlpurifier.org/ "HTML Purifier")
- [MeWebstudio/Purifier](https://github.com/mewebstudio/Purifier "MeWebstudio/Purifier")
