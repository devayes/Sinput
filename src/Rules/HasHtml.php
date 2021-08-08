<?php

namespace Devayes\Sinput\Rules;

use Illuminate\Contracts\Validation\Rule;

class HasHtml implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return ($this->stripTagsArray($value) === $value);
    }

    /**
     * Strip tags in array or string.
     * NOTE: decoding input could cause a false positive.
     *
     * @param string|array $input
     * @return string|array
     */
    public function stripTagsArray($input)
    {
        $decode = function ($str) {
            return html_entity_decode($str, ENT_QUOTES, 'UTF-8');
        };

        if (is_array($input)) {
            foreach ($input as $k => $v) {
                if (is_array($value)) {
                    $input[$k] = $this->stripTagsArray($v);
                } else {
                    $input[$k] = strip_tags($decode($v));
                }
            }
        } else {
            $input = strip_tags($decode($input));
        }

        return $input;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute field cannot contain HTML.';
    }
}
