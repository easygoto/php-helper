<?php

namespace Trink\Core\Helper;

/**
 * Trait Singleton
 * @package Trink\Core\Helper
 */
trait Singleton
{
    private static $instance;

    public static function instance(...$args)
    {
        if (! isset(static::$instance)) {
            static::$instance = new static(...$args);
        }
        return static::$instance;
    }
}
