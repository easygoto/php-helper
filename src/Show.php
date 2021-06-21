<?php

namespace Trink\Core\Helper;

use Exception;

/**
 * Class Show
 * @package Trink\Core\Helper
 * @author  trink
 */
class Show
{
    public const CLI_COLOR_BLACK = 30;
    public const CLI_COLOR_RED = 31;
    public const CLI_COLOR_GREEN = 32;
    public const CLI_COLOR_YELLOW = 33;
    public const CLI_COLOR_BLUE = 34;
    public const CLI_COLOR_PURPLE = 35;
    public const CLI_COLOR_DARK_GREEN = 36;
    public const CLI_COLOR_WHITE = 37;

    /**
     * 简版的时间分析
     *
     * @param callable|null $callback
     */
    public static function timer(?callable $callback = null): void
    {
        $start = microtime(true);
        $callback && $callback();
        self::println('runtime : ' . (microtime(true) - $start) * 1000 . ' ms');
    }

    /**
     * 转成字符串
     *
     * @param      $data
     *
     * @return string
     */
    public static function toStr($data): string
    {
        $msg = '';
        if (is_array($data) || is_object($data)) {
            try {
                $msg = json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
            } catch (Exception $e) {
            }
        } elseif (is_bool($data) || is_resource($data)) {
            $msg = var_export($data, true);
        } else {
            $msg = sprintf("%s", $data);
        }
        return $msg ?? '';
    }

    /**
     * 转成字符串(带回车)
     *
     * @param      $data
     *
     * @return string
     */
    public static function toLnStr($data): string
    {
        if (false !== stripos(PHP_SAPI, "cli")) {
            $endLine = PHP_EOL;
        } else {
            $endLine = '<br>';
        }

        return static::toStr($data) . $endLine;
    }

    /**
     * 打印数据, 不带回车
     *
     * @param $data
     *
     * @return void
     */
    public static function print($data): void
    {
        print static::toStr($data);
    }

    /**
     * 打印数据, 带回车
     *
     * @param $data
     *
     * @return void
     */
    public static function println($data): void
    {
        print static::toLnStr($data);
    }

    /**
     * 转成带有颜色的命令行字符串
     *
     * @param int          $color
     * @param string       $title
     * @param string|array $desc
     *
     * @return string
     */
    public static function toCliStr(int $color, string $title, $desc = ''): string
    {
        $title = sprintf('%-20s', $title);
        if (is_string($desc)) {
            $msg = "\e[{$color}m$title\t\e[0m$desc";
        } elseif (is_array($desc)) {
            $blank = sprintf('%-20s', '');
            $message = implode("\n$blank\t", $desc);
            $msg = "\e[{$color}m$title\t\e[0m$message";
        } else {
            $msg = "\e[{$color}m$title\t\e[0m";
        }
        return $msg;
    }

    /**
     * 转成带有颜色的命令行字符串(带有回车)
     *
     * @param int          $color
     * @param string       $title
     * @param string|array $desc
     *
     * @return string
     */
    public static function toCliLnStr(int $color, string $title, $desc = ''): string
    {
        return static::toCliStr($color, $title, $desc) . "\n";
    }

    /**
     * 带有颜色的命令行打印信息
     *
     * @param int          $color 可以使用预设的 CLI_COLOR 设置
     * @param string       $title 提示信息
     * @param string|array $desc  提示信息详情
     *
     * @return void
     */
    public static function cliLn(int $color, string $title, $desc = ''): void
    {
        print static::toCliLnStr($color, $title, $desc);
    }
}
