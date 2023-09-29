<?php

namespace dwy\CookieConsentManager\helpers;


class ArrayHelper
{
    /**
     * Array filter recursive
     *
     * @param array $input
     * @return array
     */
    public static function filter(array $array): array
    {
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                $value = self::filter($value);

                continue;
            }

            if (is_null($value) || $value === '') {
                unset($array[$key]);
            }
        }

        unset($value);

        return $array;
    }
}
