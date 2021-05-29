<?php

namespace Trink\Core\Helper;

use stdClass;

/**
 * Class Arrays
 * @package Trink\Core\Helper
 * @author trink
 */
class Arrays
{
    // 批量将数组中指定的值创建出一个新的数组
    // 数组对象互转
    // 通过点分割的方式在数组中取值
    public static function get($list, string $key = null, $default = null, $separator = '.')
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

    public static function set($list, string $key = null, $value = null, $separator = '.')
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

    public static function getInt($list, string $key, $default = ''): int
    {
        return (int)self::get($list, $key, $default);
    }

    public static function getFloat($list, string $key, $decimals = 2, $default = ''): float
    {
        return (float)number_format((float)self::get($list, $key, $default), $decimals, '.', '');
    }
}
