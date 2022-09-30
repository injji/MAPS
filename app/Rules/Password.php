<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Password implements Rule
{
    protected $errorType = 'etc';
    protected $length = null;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($length = 8)
    {
        $this->length = $length;
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
        if (strlen($value) < $this->length) {
            $this->errorType = 'length';
            return false;
        } elseif (!preg_match('/[`~!@#$%^&*|\\\'\";:\/?]/', $value)) {
            $this->errorType = 'schar';
            return false;
        }
        return preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[$@$!%*#?&])[A-Za-z\d$@$!%*#?&]{'.$this->length.',}$/', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.password.'.$this->errorType, [
            'length' => $this->length
        ]);
    }
}
