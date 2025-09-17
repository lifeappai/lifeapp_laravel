<?php

namespace App\Http\Traits;

trait LocaleTrait
{
    public function getLocaleAttribute($value)
    {
        if (empty($value)) {
            $value = 'en';
        }
        $locale = [
            'en' => 'En',
            'mr' => 'Mr',
            'hi' => 'Hi',
        ];

        return $locale[strtolower($value)];
    }
}
