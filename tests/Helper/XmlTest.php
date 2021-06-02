<?php

namespace Tests\Core\Helper;

use Tests\Core\BaseTest;
use Trink\Core\Helper\Show;
use Trink\Core\Helper\Xml;

class XmlTest extends BaseTest
{
    /** @test */
    public function xml2Array(): void
    {
        $xml = <<<XML
<xml>
    <ToUserName><![CDATA[xxx]]></ToUserName>
    <FromUserName><![CDATA[qqq]]></FromUserName>
    <CreateTime>1583308219</CreateTime>
    <MsgType><![CDATA[text]]></MsgType>
    <Content>
        <Text><![CDATA[你好]]></Text>
        <Image><![CDATA[a.jpg]]></Image>
        <Image><![CDATA[b.jpg]]></Image>
    </Content>
    <MsgId>1128850165</MsgId>
    <AgentID>1000056</AgentID>
</xml>
XML;
        $array = Xml::toArrayFromString($xml);
        Show::println($array);
        static::assertTrue(true);
    }
}
