<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Url implements Rule
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
        if (!$value) {
            return true;
        }
        if (!$value) return true;
        return preg_match('/(http(s)?:\/\/)([a-z0-9가-핳\w]\.*)+[a-z0-9]{2,4}/i', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.url');
    }
}
