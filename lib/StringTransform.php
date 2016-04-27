<?php

/*
 * This file is part of the `src-run/wonka-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 * (c) Scribe Inc      <scr@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Utility;

/**
 * Class StringTransform.
 */
class StringTransform
{
    /**
     * @param string $string
     *
     * @return string
     */
    final public static function toAlphanumeric($string)
    {
        return (string) preg_replace('/[^a-z0-9]/i', '', $string);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    final public static function toAlphanumericAndDashes($string)
    {
        return (string) preg_replace('/[^a-z0-9-]/i', '', $string);
    }

    /**
     * @param string $string
     * @param bool   $limit
     *
     * @return string
     */
    final public static function spacesToDashes($string, $limit = true)
    {
        $string = str_replace(' ', '-', $string);

        if ($limit) {
            $string = preg_replace('{-+}', '-', $string);
        }

        return (string) $string;
    }

    /**
     * @param string $string
     * @param bool   $limit
     *
     * @return string
     */
    final public static function dashesToSpaces($string, $limit = true)
    {
        $string = str_replace('-', ' ', $string);

        if ($limit) {
            $string = preg_replace('{[ ]+}', ' ', $string);
        }

        return (string) $string;
    }

    /**
     * @param string $string
     * @param bool   $lower
     *
     * @return string
     */
    final public static function toSlug($string, $lower = true)
    {
        $string = self::toAlphanumericAndDashes(self::spacesToDashes($string));

        return $lower ? (string) strtolower($string) : $string;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    final public static function toPhoneNumber($string)
    {
        $string = preg_replace('~.*(\d{3})[^\d]*(\d{3})[^\d]*(\d{4}).*~', '$1$2$3', $string);
        $string = preg_replace('/[^0-9]/', '', $string);

        if (strlen($string) === 10) {
            $string = '1'.$string;
        }

        return (string) $string;
    }

    /**
     * @param string $string
     * @param string $format
     *
     * @return string
     */
    final public static function toPhoneNumberFormatted($string, $format = '+%COUNTRY% (%NPA%) %CO%-%LINE%')
    {
        $string = self::toPhoneNumber($string);

        if (strlen($string) === 11) {
            $format = str_replace('%COUNTRY%', substr($string, 0, 1), $format);
            $format = str_replace('%NPA%', substr($string, 1, 3), $format);
            $format = str_replace('%CO%', substr($string, 4, 3), $format);
            $format = str_replace('%LINE%', substr($string, 7, 4), $format);
        }

        return (string) $format;
    }

    /**
     * @param string $a
     * @param string $b
     * @param string $encoding
     *
     * @return bool
     */
    final public static function compare($a, $b, $encoding = 'UTF-8')
    {
        mb_internal_encoding($encoding);

        $str1Split = self::split(mb_convert_case($a, MB_CASE_LOWER));
        $str2Split = self::split(mb_convert_case($b, MB_CASE_LOWER));

        if (count($str1Split) !== count($str2Split)) {
            return false;
        }

        for ($i = 0; $i < count($str1Split); ++$i) {
            if ($str1Split[$i] !== $str2Split[$i]) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $string
     *
     * @return array
     */
    final public static function split($string)
    {
        $max = mb_strlen($string);
        $result = [];

        for ($i = 0; $i < $max; ++$i) {
            $result[] = mb_substr($string, $i, 1);
        }

        return (array) $result;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    final public static function camelToSnakeCase($string)
    {
        return strtolower(preg_replace('#(?<=\\w)(?=[A-Z])#', '_$1', $string));
    }

    /**
     * @param string $string
     *
     * @return string
     */
    final public static function camelToPascalCase($string)
    {
        return ucfirst($string);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    final public static function pascalToSnakeCase($string)
    {
        return static::camelToSnakeCase($string);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    final public static function pascalToCamelCase($string)
    {
        return lcfirst($string);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    final public static function snakeToCamelCase($string)
    {
        $visitor = function ($match) {
            return strtoupper($match[2]);
        };

        return preg_replace_callback('{(_)([a-z])}', $visitor, $string);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    final public static function snakeToPascalCase($string)
    {
        return static::camelToPascalCase(static::snakeToCamelCase($string));
    }
}

/* EOF */
