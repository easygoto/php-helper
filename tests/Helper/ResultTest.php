<?php

namespace Tests\Core\Helper;

use Tests\Core\BaseTest;
use Trink\Core\Helper\Result;
use Trink\Core\Helper\Show;

class ResultTest extends BaseTest
{
    public function test(): void
    {
        Show::println(Result::success()->asArray());
        static::assertTrue(true);
    }
}
