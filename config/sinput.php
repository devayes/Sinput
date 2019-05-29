<?php

/*
 * Laravel Sinput Package.
 *
 * MIT License (MIT).
 * @link https://opensource.org/licenses/MIT
 */

return [

    /**
     * The default rule set from config/purifier.php
     * to apply to filtering. This can be an html rule
     * or a rule to strip all html by default unless
     * a new rule is specified.
     */
    'default_ruleset' => 'default',

    /**
     *  Decode html entities before scrubbing.
     *  HTMLPurifier will not process encoded HTML as
     *  it is technically safe. This option
     *  will decode any HTML and enforce the rules applied.
     */
    'decode_input' => true,

    /**
     * HTMLPurifier will return entities encoded (ie: &lt;, &gt;, &amp;, &quot;)
     * Ordinarily, this is the preferred behavior, but can cause double
     * encoding issues when a value is wrapped in blade encoding braces (ie: {{ $foo }} )
     */
    'decode_output' => true,

];