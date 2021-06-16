<?php

namespace Tests\Core\Helper;

use Trink\Core\Helper\Format;
use Trink\Core\Helper\Show;

class FormatTest extends BaseTest
{
    public function test(): void
    {
        static::assertEquals('aBCD', Format::toCamelCase('a_b_c_d'));
        static::assertEquals('a_b_c_d', Format::toUnderScore('aBCD'));

        static::assertEquals('userNamePassWord', Format::toCamelCase('user_name_pass_word'));
        static::assertEquals('user_name_pass_word', Format::toUnderScore('userNamePassWord'));

        $arr = ['status_code' => 0, 'data' => ['user_name' => 'admin', 'last_login_ip' => '127.0.0.1']];
        Show::println(Format::array2CamelCase($arr));

        $brr = ['statusCode' => 0, 'data' => ['userName' => 'admin', 'lastLoginIp' => '127.0.0.1']];
        Show::println(Format::array2UnderScore($brr));
    }
}
