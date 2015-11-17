<?php
/**
 * Copyright (c) 2011 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */

class NamingConvention {
    static public function isCamelCase($str)
    {
        return ctype_alpha($str);
    }

    static public function isUpperCamelCase($str)
    {
        if (!ctype_upper(substr($str, 0 ,1))) {
            return false;
        } elseif (!ctype_alpha($str)) {
            return false;
        }
        return true;
    }

    static public function isLowerCamelCase($str)
    {
        if (!ctype_lower(substr($str, 0 ,1))) {
            return false;
        } elseif (!ctype_alpha($str)) {
            return false;
        }
        return true;
    }

    static public function isSnakeCase($str)
    {
        $str = str_replace('_', '', $str);
        return ctype_lower($str);
    }

    static public function snakeCaseToCamelCase($str)
    {
        $camelCase = self::snakeCaseToCamelCaseFirstUpper($str);
        return lcfirst($camelCase);
    }

    static public function snakeCaseToCamelCaseFirstUpper($str)
    {
        $parts = explode('_', $str);
        foreach ($parts as $part) {
            $camelCase .= ucfirst($part);
        }
        return $camelCase;
    }

    static public function camelCaseToSnakeCase($str)
    {
        $parts = self::splitAtUpperCase($str);
        $lastIndex = count($parts) - 1;
        $index = 0;
        foreach ($parts as $part) {
            $snakeCase .= strtolower($part);
            if ($lastIndex != $index++) {
                $snakeCase .= '_';
            }
        }
        return $snakeCase;
    }

    static public function splitAtUpperCase($str) {
        return preg_split('/(?=[A-Z])/', $str, -1, PREG_SPLIT_NO_EMPTY);
    }
}
