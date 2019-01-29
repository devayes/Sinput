Laravel Sinput
==========

Sinput was created to provide simple, familiar methods to obtain and clean user input of XSS and malformed HTML using very simple to very complex rules. Sinput is a set of easy to use wrapper methods that lean on the work of the established and well supported [MeWebStudio/Purifier](https://github.com/mewebstudio/Purifier "MeWebStudio/Purifier"), a [Laravel](https://laravel.com/docs/5.7/ "Laravel") friendly implementation of [HTML Purifier](http://htmlpurifier.org/ "HTML Purifier"). Sinput provides access to a broad range of useful, Laravel-like methods to simplify input sanitization of XSS and correcting malformed HTML. 

### Compatibility
- Laravel 5.0 - 5.7
- PHP >= 5.5.9

### Installation
Install via composer.
`composer require mews/purifier`

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
        'Purifier' => Devayes\Sinput\Facades\Sinput::class,
    ]
```
### Configuration
You must publish the Mews\Purifier configuration to configure your own HTML sanitation rules.

`$ php artisan vendor:publish --provider="Mews\Purifier\PurifierServiceProvider"`

You 'll notice in the config the `'default'` setting allows a standard set of permissible HTML. I prefer stripping **all** HTML by default using this configuration:
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

Sinput decodes all HTML entities by default before sanitizing and provides option to trim output. These options can be set in code at run-time but you're welcome to over-ride the defaults using the config.

`$ php artisan vendor:publish --provider="Devayes\Sinput\SinputServiceProvider"`

### Methods
Procedural function: using above config as example.
- `sinput($var)` Strip all HTML in a variable or an array. 
- `sinput($var, 'html')` Allows HTML defined in `'html'` portion of config above.

Psuedo-static methods:
- `Sinput::setDecode([true|false])` Decode HTML entities before filtering (default: true)
- `Sinput::setTrim([true|false])` Trim output of whitespace (default: false)
- `Sinput::all()` Get all input and apply default config option or `Sinput::all('html')` to allow html as per the config.
- `Sinput::get($key, $default = 'default value', $config = [null|'html'])` Get an item from the request 
- `Sinput::only(['name', 'email', bio'], $config = [null|'html'])` Get items from the request by keys.
- `Sinput::except(['_token'], $config = [null|'html'])` Get all items *except* those specified.
- `Sinput::map(['foo' => 'bar'], $config = [null|'html'])` Retrieve items from request by keys, but change index to value. IE: `['foo' => 'bar']` will retrieve *foo* and return the value of foo as *bar*.
- `Sinput::old($key, $default = null, $config = [null|'html'])` Similar to Laravel's `$request->old()` method, but able to scrub HTML or apply config rules.
- `Sinput::clean($value, $config = [null|'html'])` Clean an array or single variable.

### Thank you
- [HTML Purifier](http://htmlpurifier.org/ "HTML Purifier")
- [MeWebstudio/Purifier](https://github.com/mewebstudio/Purifier "MeWebstudio/Purifier")
