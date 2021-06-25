<?php

namespace Trink\Core\Helper;

/**
 * Class Format
 * @package Trink\Core\Helper
 * @author  trink
 */
class Format
{
    /**
     * 下划线转驼峰
     *
     * @param        $words
     * @param string $separator
     *
     * @return string
     */
    public static function toCamelCase($words, string $separator = '_'): string
    {
        $words = $separator . str_replace($separator, " ", strtolower($words));
        return ltrim(str_replace(" ", "", ucwords($words)), $separator);
    }

    /**
     * 驼峰命名转下划线
     *
     * @param        $words
     * @param string $separator
     *
     * @return string
     */
    public static function toUnderScore($words, string $separator = '_'): string
    {
        $str = strtolower(preg_replace('/([a-z])?([A-Z])/', "$1" . $separator . "$2", $words));
        return trim($str, $separator);
    }

    /**
     * 数组所有下划线名转驼峰
     *
     * @param array  $list
     * @param string $separator
     *
     * @return array
     */
    public static function array2CamelCase(array $list, string $separator = '_'): array
    {
        $temp = [];
        foreach ($list as $key => $value) {
            $newKey = self::toCamelCase($key, $separator);
            if (is_array($value)) {
                $value = self::array2CamelCase($value, $separator);
            }
            $temp[$newKey] = $value;
        }
        return $temp;
    }

    /**
     * 数组所有驼峰命名转下划线
     *
     * @param array  $list
     * @param string $separator
     *
     * @return array
     */
    public static function array2UnderScore(array $list, string $separator = '_'): array
    {
        $temp = [];
        foreach ($list as $key => $value) {
            $newKey = self::toUnderScore($key, $separator);
            if (is_array($value)) {
                $value = self::array2UnderScore($value, $separator);
            }
            $temp[$newKey] = $value;
        }
        return $temp;
    }

    public static function filterFieldsInclude($list, $include = []): array
    {
        $temp = [];
        foreach ($list as $key => $value) {
            if (is_array($value)) {
                $temp[$key] = self::filterFieldsInclude($value, $include);
                continue;
            }
            if (in_array($key, $include, true)) {
                $temp[$key] = $value;
            }
        }
        return $temp;
    }

    public static function filterFieldsExclude($list, $exclude = []): array
    {
        $temp = [];
        foreach ($list as $key => $value) {
            if (is_array($value)) {
                $temp[$key] = self::filterFieldsExclude($value, $exclude);
                continue;
            }
            if (!in_array($key, $exclude, true)) {
                $temp[$key] = $value;
            }
        }
        return $temp;
    }
}
