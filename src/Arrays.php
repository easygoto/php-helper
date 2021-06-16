<?php

namespace Trink\Core\Helper;

use stdClass;

/**
 * Class Arrays
 * @package Trink\Core\Helper
 * @author  trink
 */
class Arrays
{
    /**
     * 通过点分割的方式在数组中取值
     *
     * @param             $list
     * @param string|null $key
     * @param null        $default
     * @param string      $separator
     *
     * @return mixed|null
     */
    public static function get($list, string $key = null, $default = null, string $separator = '.')
    {
        if ($key === null) {
            return $list;
        }
        if (strpos($key, $separator) !== false) {
            $keyList = explode($separator, $key);
            $temp = $list;
            foreach ($keyList as $item) {
                if (is_array($list)) {
                    $temp = $temp[$item] ?? $default;
                } elseif (is_object($list)) {
                    $temp = $temp->$item ?? $default;
                } else {
                    return $default;
                }
            }
            return $temp;
        }
        if (is_array($list)) {
            return $list[$key] ?? $default;
        }

        if (is_object($list)) {
            return $list->$key ?? $default;
        }

        return $default;
    }

    /**
     * 通过点分割的方式在数组中设置值
     *
     * @param array|object $list
     * @param string|null  $key
     * @param null         $value
     * @param string       $separator
     *
     * @return array|object
     */
    public static function set($list, string $key = null, $value = null, string $separator = '.')
    {
        if ($key === null) {
            return $list;
        }

        if (strpos($key, $separator) !== false) {
            $keyList = explode($separator, $key);
            if (is_array($list)) {
                $codeKey = implode('', array_map(static fn ($itemKey) => "['$itemKey']", $keyList));
                eval(sprintf('$list%s = $value;', $codeKey));
            } else {
                foreach ($keyList as $item) {
                    if (is_object($list)) {
                        $temp = $temp->$item ?? new stdClass();
                    } else {
                        return $list;
                    }
                }
            }

            return $list;
        }

        if (is_array($list)) {
            $list[$key] = $value;
        }

        if (is_object($list)) {
            $list->$key = $value;
        }

        return $list;
    }

    /**
     * 获取整型
     *
     * @param        $list
     * @param string $key
     * @param string $default
     *
     * @return int
     */
    public static function getInt($list, string $key, string $default = ''): int
    {
        return (int)self::get($list, $key, $default);
    }

    /**
     * 获取浮点型
     *
     * @param        $list
     * @param string $key
     * @param int    $decimals
     * @param string $default
     *
     * @return float
     */
    public static function getFloat($list, string $key, int $decimals = 2, string $default = ''): float
    {
        return (float)number_format((float)self::get($list, $key, $default), $decimals, '.', '');
    }
}
