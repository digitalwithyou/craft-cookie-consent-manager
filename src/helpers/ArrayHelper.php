<?php

namespace dwy\CookieConsentManager\helpers;

use craft\helpers\ArrayHelper as CraftArrayHelper;

class ArrayHelper extends CraftArrayHelper
{
    /**
     * Array filter recursive
     *
     * @param array $input
     * @return array
     */
    public static function filter(array $input): array
    {
        $output = [];

        foreach ($input as $key => $value) {
            if (is_array($value)) {
                $output[$key] = self::filter($value);

                if (empty($output[$key])) {
                    unset($output[$key]);
                }

                continue;
            }

            $output[$key] = array_filter($value);
        }

        return $output;
    }
}
