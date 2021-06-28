<?php

namespace Tests\Core\Helper;

use Exception;
use Trink\Core\Helper\Toolkit;

class ToolkitTest extends BaseTest
{
    public function testThrowable2Array(): void
    {
        $errArray = Toolkit::throwable2Array(new Exception('msg', 123));
        static::assertIsArray($errArray);
        static::assertArrayHasKey('code', $errArray);
        static::assertArrayHasKey('line', $errArray);
        static::assertArrayHasKey('file', $errArray);
        static::assertArrayHasKey('message', $errArray);
        static::assertArrayHasKey('trace', $errArray);
    }
}
