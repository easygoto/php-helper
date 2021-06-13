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
     * 打印数据, 不带回车
     *
     * @param $data
     *
     * @return string
     */
    public static function print($data): string
    {
        $msg = '';
        if (is_array($data) || is_object($data)) {
            try {
                $msg = json_encode($data, JSON_THROW_ON_ERROR);
            } catch (Exception $e) {
            }
        } elseif (is_bool($data) || is_resource($data)) {
            $msg = var_export($data, true);
        } else {
            $msg = sprintf("%s", $data);
        }

        print $msg;
        return $msg ?? '';
    }

    /**
     * 打印数据, 带回车
     *
     * @param $data
     *
     * @return string
     */
    public static function println($data): string
    {
        if (false !== stripos(PHP_SAPI, "cli")) {
            $endLine = PHP_EOL;
        } else {
            $endLine = '<br>';
        }

        $msg = self::print($data);
        print $endLine;
        return $msg . $endLine;
    }

    /**
     * 带有颜色的命令行打印信息
     *
     * @param int          $color 可以使用预设的 CLI_COLOR 设置
     * @param string       $title 提示信息
     * @param string|array $desc  提示信息详情
     *
     * @return string
     */
    public static function cliLn(int $color, string $title, $desc = ''): string
    {
        $title = sprintf('%-20s', $title);
        if (is_string($desc)) {
            $msg = "\e[{$color}m$title\t\e[0m$desc\n";
        } elseif (is_array($desc)) {
            $blank = sprintf('%-20s', '');
            $message = implode("\n$blank\t", $desc);
            $msg = "\e[{$color}m$title\t\e[0m$message\n";
        } else {
            $msg = "\e[{$color}m$title\t\e[0m\n";
        }
        print $msg;
        return $msg;
    }
}
