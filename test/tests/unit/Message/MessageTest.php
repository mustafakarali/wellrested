<?php

namespace WellRESTed\Test\Unit\Message;

/**
 * @uses WellRESTed\Message\Message
 * @uses WellRESTed\Message\HeaderCollection
 */
class MessageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers WellRESTed\Message\Message::__construct
     */
    public function testCreatesInstance()
    {
        $message = $this->getMockForAbstractClass('\WellRESTed\Message\Message');
        $this->assertNotNull($message);
    }

    /**
     * @covers WellRESTed\Message\Message::getProtocolVersion
     */
    public function testReturnsProtocolVersion1Point1ByDefault()
    {
        $message = $this->getMockForAbstractClass('\WellRESTed\Message\Message');
        $this->assertEquals("1.1", $message->getProtocolVersion());
    }

    /**
     * @covers WellRESTed\Message\Message::getProtocolVersion
     */
    public function testReturnsProtocolVersion()
    {
        $message = $this->getMockForAbstractClass('\WellRESTed\Message\Message');
        $message = $message->withProtocolVersion("1.0");
        $this->assertEquals("1.0", $message->getProtocolVersion());
    }

    /**
     * @covers WellRESTed\Message\Message::withProtocolVersion
     */
    public function testReplacesProtocolVersion()
    {
        $message = $this->getMockForAbstractClass('\WellRESTed\Message\Message');
        $message = $message->withProtocolVersion("1.0");
        $this->assertEquals("1.0", $message->getProtocolVersion());
    }

    /**
     * @covers WellRESTed\Message\Message::withHeader
     */
    public function testWithHeaderSetsHeader()
    {
        $message = $this->getMockForAbstractClass('\WellRESTed\Message\Message');
        $message = $message->withHeader("Content-type", "application/json");
        $this->assertEquals(["application/json"], $message->getHeader("Content-type"));
    }

    /**
     * @covers WellRESTed\Message\Message::withHeader
     */
    public function testWithHeaderReplacesValue()
    {
        $message = $this->getMockForAbstractClass('\WellRESTed\Message\Message');
        $message = $message->withHeader("Set-Cookie", "cat=Molly");
        $message = $message->withHeader("Set-Cookie", "dog=Bear");
        $cookies = $message->getHeader("Set-Cookie");
        $this->assertNotContains("cat=Molly", $cookies);
        $this->assertContains("dog=Bear", $cookies);
    }

    /**
     * @covers WellRESTed\Message\Message::withAddedHeader
     */
    public function testWithAddedHeaderSetsHeader()
    {
        $message = $this->getMockForAbstractClass('\WellRESTed\Message\Message');
        $message = $message->withAddedHeader("Content-type", "application/json");
        $this->assertEquals(["application/json"], $message->getHeader("Content-type"));
    }

    /**
     * @covers WellRESTed\Message\Message::withAddedHeader
     */
    public function testWithAddedHeaderAppendsValue()
    {
        $message = $this->getMockForAbstractClass('\WellRESTed\Message\Message');
        $message = $message->withAddedHeader("Set-Cookie", "cat=Molly");
        $message = $message->withAddedHeader("Set-Cookie", "dog=Bear");
        $cookies = $message->getHeader("Set-Cookie");
        $this->assertContains("cat=Molly", $cookies);
        $this->assertContains("dog=Bear", $cookies);
    }

    /**
     * @covers WellRESTed\Message\Message::withoutHeader
     */
    public function testWithoutHeaderRemovesHeader()
    {
        $message = $this->getMockForAbstractClass('\WellRESTed\Message\Message');
        $message = $message->withHeader("Content-type", "application/json");
        $message = $message->withoutHeader("Content-type");
        $this->assertFalse($message->hasHeader("Content-type"));
    }

    /**
     * @covers WellRESTed\Message\Message::getHeader
     */
    public function testGetHeaderReturnsSingleHeader()
    {
        $message = $this->getMockForAbstractClass('\WellRESTed\Message\Message');
        $message = $message->withAddedHeader("Content-type", "application/json");
        $this->assertEquals(["application/json"], $message->getHeader("Content-type"));
    }

    /**
     * @covers WellRESTed\Message\Message::getHeaderLine
     */
    public function testGetHeaderReturnsMultipleHeadersJoinedByCommas()
    {
        $message = $this->getMockForAbstractClass('\WellRESTed\Message\Message');
        $message = $message->withAddedHeader("X-name", "cat=Molly");
        $message = $message->withAddedHeader("X-name", "dog=Bear");
        $this->assertEquals("cat=Molly, dog=Bear", $message->getHeaderLine("X-name"));
    }

    /**
     * @covers WellRESTed\Message\Message::getHeaderLine
     */
    public function testGetHeaderLineReturnsNullForUnsetHeader()
    {
        $message = $this->getMockForAbstractClass('\WellRESTed\Message\Message');
        $this->assertNull($message->getHeaderLine("X-not-set"));
    }

    /**
     * @covers WellRESTed\Message\Message::getHeader
     */
    public function testGetHeaderReturnsMultipleValuesForHeader()
    {
        $message = $this->getMockForAbstractClass('\WellRESTed\Message\Message');
        $message = $message->withAddedHeader("X-name", "cat=Molly");
        $message = $message->withAddedHeader("X-name", "dog=Bear");
        $this->assertEquals(["cat=Molly", "dog=Bear"], $message->getHeader("X-name"));
    }

    /**
     * @covers WellRESTed\Message\Message::getHeader
     */
    public function testGetHeaderReturnsEmptyArrayForUnsetHeader()
    {
        $message = $this->getMockForAbstractClass('\WellRESTed\Message\Message');
        $this->assertEquals([], $message->getHeader("X-name"));
    }

    /**
     * @covers WellRESTed\Message\Message::hasHeader
     */
    public function testHasHeaderReturnsTrueWhenHeaderIsSet()
    {
        $message = $this->getMockForAbstractClass('\WellRESTed\Message\Message');
        $message = $message->withHeader("Content-type", "application/json");
        $this->assertTrue($message->hasHeader("Content-type"));
    }

    /**
     * @covers WellRESTed\Message\Message::hasHeader
     */
    public function testHasHeaderReturnsFalseWhenHeaderIsNotSet()
    {
        $message = $this->getMockForAbstractClass('\WellRESTed\Message\Message');
        $this->assertFalse($message->hasHeader("Content-type"));
    }

    /**
     * @covers WellRESTed\Message\Message::getHeaders
     */
    public function testGetHeadersReturnOriginalHeaderNamesAsKeys()
    {
        $message = $this->getMockForAbstractClass('\WellRESTed\Message\Message');
        $message = $message->withHeader("Set-Cookie", "cat=Molly");
        $message = $message->withAddedHeader("Set-Cookie", "dog=Bear");
        $message = $message->withHeader("Content-type", "application/json");

        $headers = [];
        foreach ($message->getHeaders() as $key => $values) {
            $headers[] = $key;
        }

        $expected = ["Content-type", "Set-Cookie"];
        $this->assertEquals(0, count(array_diff($expected, $headers)));
        $this->assertEquals(0, count(array_diff($headers, $expected)));
    }

    /**
     * @covers WellRESTed\Message\Message::getHeaders
     */
    public function testGetHeadersReturnOriginalHeaderNamesAndValues()
    {
        $message = $this->getMockForAbstractClass('\WellRESTed\Message\Message');
        $message = $message->withHeader("Set-Cookie", "cat=Molly");
        $message = $message->withAddedHeader("Set-Cookie", "dog=Bear");
        $message = $message->withHeader("Content-type", "application/json");

        $headers = [];

        foreach ($message->getHeaders() as $key => $values) {
            foreach ($values as $value) {
                if (isset($headers[$key])) {
                    $headers[$key][] = $value;
                } else {
                    $headers[$key] = [$value];
                }
            }
        }

        $expected = [
            "Set-Cookie" => ["cat=Molly", "dog=Bear"],
            "Content-type" => ["application/json"]
        ];

        $this->assertEquals($expected, $headers);
    }

    /**
     * @covers WellRESTed\Message\Message::getBody
     * @uses WellRESTed\Stream\NullStream
     */
    public function testGetBodyReturnsEmptyStreamByDefault()
    {
        $message = $this->getMockForAbstractClass('\WellRESTed\Message\Message');
        $this->assertEquals("", (string) $message->getBody());
    }

    /**
     * @covers WellRESTed\Message\Message::getBody
     * @covers WellRESTed\Message\Message::withBody
     */
    public function testGetBodyReturnsAttachedStream()
    {
        $stream = $this->prophesize('\Psr\Http\Message\StreamableInterface');
        $stream = $stream->reveal();

        $message = $this->getMockForAbstractClass('\WellRESTed\Message\Message');
        $message = $message->withBody($stream);
        $this->assertSame($stream, $message->getBody());
    }

    /**
     * @covers WellRESTed\Message\Message::__clone
     */
    public function testCloneMakesDeepCopyOfHeaders()
    {
        $message1 = $this->getMockForAbstractClass('\WellRESTed\Message\Message');
        $message1 = $message1->withHeader("Content-type", "text/plain");
        $message2 = $message1->withHeader("Content-type", "application/json");
        $this->assertEquals(["text/plain"], $message1->getHeader("Content-type"));
        $this->assertEquals(["application/json"], $message2->getHeader("Content-type"));
    }
}
