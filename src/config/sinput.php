<?php

/*
 * Laravel Sinput Package.
 *
 * MIT License (MIT).
 * @link https://opensource.org/licenses/MIT
 */

return [

    /**
     * The default ruleset to apply to filtering.
     * This can be an html rule or a rule to strip all html
     * or create your own custom ruleset dpecific to your needs.
     */
    'default_ruleset' => 'no_html',

    /**
     * Specify a default ruleset for route or web middleware from the purifier rulesets below.
     * Use a permissive ruleset (ie: "html" in the docs) to allow html while removing xss and
     * correct malformed html -or- you can use a restrictive ruleset
     * (ie: "no_html") to strip all html from input.
     * When using as a route middleware, this can be over-ridden on a per route basis.
     * eg: Route::post('/article/save', ['middleware' => 'sinput:html', 'uses' => 'ArticlesController@postSave']);
     */
    'middleware_ruleset' => 'allow_html',

    /**
     *  Decode html entities before scrubbing.
     *  HTMLPurifier will not process encoded HTML as
     *  it is technically safe. This option
     *  will decode any HTML and enforce the rules to be alied applied.
     */
    'decode_input' => true,

    /**
     * HTMLPurifier will return entities encoded (ie: &lt;, &gt;, &amp;, &quot;)
     * Ordinarily, this is the preferred behavior, but can cause double
     * encoding issues when a value is wrapped in blade encoding braces (ie: {{ $foo }} )
     */
    'decode_output' => true,

    /**
     * HTMLPurifier options & rulesets
     */
    'purifier' => [
        'encoding' => 'UTF-8', // Core.Encoding
        'finalize' => true, // Finalizes a configuration object, prohibiting further changes.
        'cache_path' => storage_path('app/purifier'), // Cache.SerializerPath
        'cache_file_mode' => 0755, // Cache.SerializerPermissions

        /**
         * See: http://htmlpurifier.org/live/configdoc/plain.html
         */
        'rulesets' => [
            // Do not allow any html at all.
            'no_html' => [
                'HTML.Doctype' => 'HTML 4.01 Transitional',
                'Core.Encoding' => 'UTF-8',
                'URI.DisableExternalResources' => true,
                'HTML.Allowed' => '',
            ],
            // Allow a permissive set of html to exist in user input.
            'allow_html' => [
                'HTML.Doctype'             => 'HTML 4.01 Transitional',
                'HTML.Allowed'             => 'div,b,strong,i,em,u,a[href|title],ul,ol,li,p[style],br,span[style],img[width|height|alt|src]',
                'CSS.AllowedProperties'    => 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align',
                'AutoFormat.AutoParagraph' => false,
                'AutoFormat.RemoveEmpty'   => true,
            ],
        ]
    ],

];
