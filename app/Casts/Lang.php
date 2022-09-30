<?php

namespace App\Casts;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class Lang implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return string
     */
    public function get($model, $key, $value, $attributes)
    {
        foreach (config('app.lang') as $key => $lang) {
            if ($value == $lang) {
                return $key;
            }
        }
        return '';
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  string  $value
     * @param  array  $attributes
     * @return array
     */
    public function set($model, $key, $value, $attributes)
    {
        return config('app.lang.'.$value);
    }
}
