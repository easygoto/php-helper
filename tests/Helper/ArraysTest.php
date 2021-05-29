<?php

namespace Tests\Core\Helper;

use Tests\Core\BaseTest;
use Trink\Core\Helper\Arrays;
use Trink\Core\Helper\Show;

class ArraysTest extends BaseTest
{
    public function test(): void
    {
        $arr = ['a' => ['b' => ['c' => ['d' => 42]], 'e' => 45, 'f' => 321], 'g' => 112];
        Show::println(Arrays::get($arr, 'a.b.c'));

        Show::println(Arrays::set($arr, 'a.b.c.h', 82));
        static::assertTrue(true);
    }
}
