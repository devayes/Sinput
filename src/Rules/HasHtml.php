<?php

namespace Devayes\Sinput\Rules;

use Illuminate\Contracts\Validation\Rule;

class HasHTML implements Rule
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
        return (strip_tags(html_entity_decode($value, ENT_QUOTES, 'UTF-8')) === $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute input contains HTML.';
    }
}
