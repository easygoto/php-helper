<?php

namespace Trink\Core\Helper;

use SimpleXMLElement;

/**
 * Class Xml
 * @package Trink\Core\Helper
 * @author  trink
 */
class Xml
{
    /**
     * 解析 xml 字符串到 array
     *
     * @param string $xml
     *
     * @return array|string
     */
    public static function toArrayFromString(string $xml)
    {
        return self::toArray(simplexml_load_string($xml));
    }

    protected static function toArray(SimpleXMLElement $xml)
    {
        if (count($xml) >= 1) {
            $result = $keys = [];
            foreach ($xml as $key => $value) {
                isset($keys[$key]) ? (++$keys[$key]) : ($keys[$key] = 1);

                if ($keys[$key] === 1) {
                    $result[$key] = self::toArray($value);
                } elseif ($keys[$key] === 2) {
                    $result[$key] = [$result[$key], self::toArray($value)];
                } elseif ($keys[$key] > 2) {
                    $result[$key][] = self::toArray($value);
                }
            }
            return $result;
        }

        if (count($xml) === 0) {
            return (string)$xml;
        }
        return [];
    }
}
