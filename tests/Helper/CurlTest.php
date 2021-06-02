<?php

namespace Tests\Core\Helper;

use Tests\Core\BaseTest;
use Trink\Core\Helper\Curl;
use Trink\Core\Helper\Show;

class CurlTest extends BaseTest
{
    public function test()
    {
        Show::println(Curl::get('https://example.com'));
        static::assertTrue(true);
    }
}
