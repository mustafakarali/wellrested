<?php

namespace WellRESTed\Test\Unit\Message;

use WellRESTed\Message\ServerRequest;

class ServerRequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers WellRESTed\Message\ServerRequest::__construct
     * @uses WellRESTed\Message\Message
     * @uses WellRESTed\Message\HeaderCollection
     */
    public function testCreatesInstance()
    {
        $request = new ServerRequest();
        $this->assertNotNull($request);
    }

    /**
     * @covers WellRESTed\Message\ServerRequest::getServerRequest
     * @covers WellRESTed\Message\ServerRequest::updateWithServerRequest
     * @covers WellRESTed\Message\ServerRequest::getServerRequestHeaders
     * @uses WellRESTed\Message\ServerRequest::__construct
     * @uses WellRESTed\Message\ServerRequest::__clone
     * @uses WellRESTed\Message\Request
     * @uses WellRESTed\Message\Message
     * @uses WellRESTed\Message\HeaderCollection
     * @preserveGlobalState disabled
     */
    public function testGetServerRequestReadsFromRequest()
    {
        $_SERVER = [
            "HTTP_HOST" => "localhost",
            "HTTP_ACCEPT" => "application/json",
            "QUERY_STRING" => "guinea_pig=Claude&hamster=Fizzgig"
        ];
        $_COOKIE = [
            "cat" => "Molly"
        ];
        $_FILES = [
            "file" => [
                "name" => "MyFile.jpg",
                "type" => "image/jpeg",
                "tmp_name" => "/tmp/php/php6hst32",
                "error" => "UPLOAD_ERR_OK",
                "size" => 98174
            ]
        ];
        $request = ServerRequest::getServerRequest();
        $this->assertNotNull($request);
    }

    /**
     * @covers WellRESTed\Message\ServerRequest::withServerRequest
     * @covers WellRESTed\Message\ServerRequest::updateWithServerRequest
     * @covers WellRESTed\Message\ServerRequest::getServerRequestHeaders
     * @uses WellRESTed\Message\ServerRequest::__construct
     * @uses WellRESTed\Message\ServerRequest::__clone
     * @uses WellRESTed\Message\Request
     * @uses WellRESTed\Message\Message
     * @uses WellRESTed\Message\HeaderCollection
     * @preserveGlobalState disabled
     */
    public function testWithServerRequestReadsFromRequest()
    {
        $_SERVER = [
            "HTTP_HOST" => "localhost",
            "HTTP_ACCEPT" => "application/json",
            "QUERY_STRING" => "guinea_pig=Claude&hamster=Fizzgig"
        ];
        $_COOKIE = [
            "cat" => "Molly"
        ];
        $_FILES = [
            "file" => [
                "name" => "MyFile.jpg",
                "type" => "image/jpeg",
                "tmp_name" => "/tmp/php/php6hst32",
                "error" => "UPLOAD_ERR_OK",
                "size" => 98174
            ]
        ];
        $request = new ServerRequest();
        $request = $request->withServerRequest();
        $this->assertNotNull($request);
        return $request;
    }

    /**
     * @covers WellRESTed\Message\ServerRequest::getServerParams
     * @preserveGlobalState disabled
     * @depends testWithServerRequestReadsFromRequest
     */
    public function testServerRequestProvidesServerParams($request)
    {
        /** @var ServerRequest $request */
        $this->assertEquals("localhost", $request->getServerParams()["HTTP_HOST"]);
    }

    /**
     * @covers WellRESTed\Message\ServerRequest::getCookieParams
     * @preserveGlobalState disabled
     * @depends testWithServerRequestReadsFromRequest
     */
    public function testServerRequestProvidesCookieParams($request)
    {
        /** @var ServerRequest $request */
        $this->assertEquals("Molly", $request->getCookieParams()["cat"]);
    }

    /**
     * @covers WellRESTed\Message\ServerRequest::getQueryParams
     * @preserveGlobalState disabled
     * @depends testWithServerRequestReadsFromRequest
     */
    public function testServerRequestProvidesQueryParams($request)
    {
        /** @var ServerRequest $request */
        $this->assertEquals("Claude", $request->getQueryParams()["guinea_pig"]);
    }

    /**
     * @covers WellRESTed\Message\ServerRequest::getFileParams
     * @preserveGlobalState disabled
     * @depends testWithServerRequestReadsFromRequest
     */
    public function testServerRequestProvidesFilesParams($request)
    {
        /** @var ServerRequest $request */
        $this->assertEquals("MyFile.jpg", $request->getFileParams()["file"]["name"]);
    }

    /**
     * @covers WellRESTed\Message\ServerRequest::getFileParams
     * @uses WellRESTed\Message\Request
     * @uses WellRESTed\Message\Message
     * @uses WellRESTed\Message\HeaderCollection
     * @preserveGlobalState disabled
     * @depends testWithServerRequestReadsFromRequest
     */
    public function testServerRequestProvidesHeaders($request)
    {
        /** @var ServerRequest $request */
        $this->assertEquals("application/json", $request->getHeader("Accept"));
    }

    /**
     * @covers WellRESTed\Message\ServerRequest::withCookieParams
     * @uses WellRESTed\Message\ServerRequest::getCookieParams
     * @uses WellRESTed\Message\ServerRequest::__clone
     * @uses WellRESTed\Message\Request
     * @uses WellRESTed\Message\Message
     * @preserveGlobalState disabled
     * @depends testWithServerRequestReadsFromRequest
     */
    public function testWithCookieParamsCreatesNewInstance($request1)
    {
        /** @var ServerRequest $request1 */
        $request2 = $request1->withCookieParams([
            "cat" => "Oscar"
        ]);
        $this->assertEquals("Molly", $request1->getCookieParams()["cat"]);
        $this->assertEquals("Oscar", $request2->getCookieParams()["cat"]);
    }

    /**
     * @covers WellRESTed\Message\ServerRequest::withQueryParams
     * @uses WellRESTed\Message\ServerRequest::getQueryParams
     * @uses WellRESTed\Message\ServerRequest::__clone
     * @uses WellRESTed\Message\Request
     * @uses WellRESTed\Message\Message
     * @preserveGlobalState disabled
     * @depends testWithServerRequestReadsFromRequest
     */
    public function testWithQueryParamsCreatesNewInstance($request1)
    {
        /** @var ServerRequest $request1 */
        $request2 = $request1->withQueryParams([
            "guinea_pig" => "Clyde"
        ]);
        $this->assertEquals("Claude", $request1->getQueryParams()["guinea_pig"]);
        $this->assertEquals("Clyde", $request2->getQueryParams()["guinea_pig"]);
    }

    /**
     * @covers WellRESTed\Message\ServerRequest::getParsedBody
     * @covers WellRESTed\Message\ServerRequest::updateWithServerRequest
     * @uses WellRESTed\Message\ServerRequest::__construct
     * @uses WellRESTed\Message\ServerRequest::__clone
     * @uses WellRESTed\Message\ServerRequest::withServerRequest
     * @uses WellRESTed\Message\ServerRequest::updateWithServerRequest
     * @uses WellRESTed\Message\ServerRequest::getServerRequestHeaders
     * @uses WellRESTed\Message\Request
     * @uses WellRESTed\Message\Message
     * @uses WellRESTed\Message\HeaderCollection
     * @preserveGlobalState disabled
     * @dataProvider formContentTypeProvider
     */
    public function testGetParsedBodyReturnsFormFieldsForUrlencodedForm($contentType)
    {
        $_SERVER = [
            "HTTP_HOST" => "localhost",
            "HTTP_CONTENT_TYPE" => $contentType,
        ];
        $_COOKIE = [];
        $_FILES = [];
        $_POST = [
            "dog" => "Bear"
        ];
        $request = new ServerRequest();
        $request = $request->withServerRequest();
        $this->assertEquals("Bear", $request->getParsedBody()["dog"]);
    }

    public function formContentTypeProvider()
    {
        return [
            ["application/x-www-form-urlencoded"],
            ["multipart/form-data"]
        ];
    }

    /**
     * @covers WellRESTed\Message\ServerRequest::withParsedBody
     * @uses WellRESTed\Message\ServerRequest::getParsedBody
     * @uses WellRESTed\Message\ServerRequest::__construct
     * @uses WellRESTed\Message\ServerRequest::__clone
     * @uses WellRESTed\Message\ServerRequest::withServerRequest
     * @uses WellRESTed\Message\ServerRequest::updateWithServerRequest
     * @uses WellRESTed\Message\ServerRequest::getServerRequestHeaders
     * @uses WellRESTed\Message\Request
     * @uses WellRESTed\Message\Message
     * @uses WellRESTed\Message\HeaderCollection
     * @preserveGlobalState disabled
     */
    public function testWithParsedBodyCreatesNewInstance()
    {
        $_SERVER = [
            "HTTP_HOST" => "localhost",
            "HTTP_CONTENT_TYPE" => "application/x-www-form-urlencoded",
        ];
        $_COOKIE = [];
        $_FILES = [];
        $_POST = [
            "dog" => "Bear"
        ];
        $request1 = new ServerRequest();
        $request1 = $request1->withServerRequest();
        $body1 = $request1->getParsedBody();

        $request2 = $request1->withParsedBody([
            "guinea_pig" => "Clyde"
        ]);
        $body2 = $request2->getParsedBody();

        $this->assertEquals("Bear", $body1["dog"]);
        $this->assertEquals("Clyde", $body2["guinea_pig"]);
    }

    /**
     * @covers WellRESTed\Message\ServerRequest::__clone
     * @uses WellRESTed\Message\ServerRequest::__construct
     * @uses WellRESTed\Message\ServerRequest::withParsedBody
     * @uses WellRESTed\Message\ServerRequest::getParsedBody
     * @uses WellRESTed\Message\Request
     * @uses WellRESTed\Message\Message
     * @uses WellRESTed\Message\HeaderCollection
     */
    public function testCloneMakesDeepCopiesOfParsedBody()
    {
        $body = (object) [
            "cat" => "Dog"
        ];

        $request1 = new ServerRequest();
        $request1 = $request1->withParsedBody($body);
        $request2 = $request1->withHeader("X-extra", "hello world");
        $this->assertEquals($request1->getParsedBody(), $request2->getParsedBody());
        $this->assertNotSame($request1->getParsedBody(), $request2->getParsedBody());
    }

    /**
     * @covers WellRESTed\Message\ServerRequest::withAttribute
     * @covers WellRESTed\Message\ServerRequest::getAttribute
     * @uses WellRESTed\Message\ServerRequest::__construct
     * @uses WellRESTed\Message\ServerRequest::__clone
     * @uses WellRESTed\Message\ServerRequest::withParsedBody
     * @uses WellRESTed\Message\ServerRequest::getParsedBody
     * @uses WellRESTed\Message\Request
     * @uses WellRESTed\Message\Message
     * @uses WellRESTed\Message\HeaderCollection
     */
    public function testWithAttributeCreatesNewInstance()
    {
        $request = new ServerRequest();
        $request = $request->withAttribute("cat", "Molly");
        $this->assertEquals("Molly", $request->getAttribute("cat"));
    }

    /**
     * @covers WellRESTed\Message\ServerRequest::withAttribute
     * @uses WellRESTed\Message\ServerRequest::getAttribute
     * @uses WellRESTed\Message\ServerRequest::__construct
     * @uses WellRESTed\Message\ServerRequest::__clone
     * @uses WellRESTed\Message\ServerRequest::withParsedBody
     * @uses WellRESTed\Message\ServerRequest::getParsedBody
     * @uses WellRESTed\Message\Request
     * @uses WellRESTed\Message\Message
     * @uses WellRESTed\Message\HeaderCollection
     */
    public function testWithAttributePreserversOtherAttributes()
    {
        $request = new ServerRequest();
        $request = $request->withAttribute("cat", "Molly");
        $request = $request->withAttribute("dog", "Bear");
        $this->assertEquals("Molly", $request->getAttribute("cat"));
        $this->assertEquals("Bear", $request->getAttribute("dog"));
    }

    /**
     * @covers WellRESTed\Message\ServerRequest::getAttribute
     * @uses WellRESTed\Message\ServerRequest::__construct
     * @uses WellRESTed\Message\Request
     * @uses WellRESTed\Message\Message
     * @uses WellRESTed\Message\HeaderCollection
     */
    public function testGetAttributeReturnsDefaultIfNotSet()
    {
        $request = new ServerRequest();
        $this->assertEquals("Oscar", $request->getAttribute("cat", "Oscar"));
    }

    /**
     * @covers WellRESTed\Message\ServerRequest::withoutAttribute
     * @uses WellRESTed\Message\ServerRequest::withAttribute
     * @uses WellRESTed\Message\ServerRequest::getAttribute
     * @uses WellRESTed\Message\ServerRequest::__construct
     * @uses WellRESTed\Message\ServerRequest::__clone
     * @uses WellRESTed\Message\ServerRequest::withParsedBody
     * @uses WellRESTed\Message\ServerRequest::getParsedBody
     * @uses WellRESTed\Message\Request
     * @uses WellRESTed\Message\Message
     * @uses WellRESTed\Message\HeaderCollection
     */
    public function testWithoutAttributeCreatesNewInstance()
    {
        $request = new ServerRequest();
        $request = $request->withAttribute("cat", "Molly");
        $request = $request->withoutAttribute("cat");
        $this->assertEquals("Oscar", $request->getAttribute("cat", "Oscar"));
    }

    /**
     * @covers WellRESTed\Message\ServerRequest::withoutAttribute
     * @uses WellRESTed\Message\ServerRequest::withAttribute
     * @uses WellRESTed\Message\ServerRequest::getAttribute
     * @uses WellRESTed\Message\ServerRequest::__construct
     * @uses WellRESTed\Message\ServerRequest::__clone
     * @uses WellRESTed\Message\ServerRequest::withParsedBody
     * @uses WellRESTed\Message\ServerRequest::getParsedBody
     * @uses WellRESTed\Message\Request
     * @uses WellRESTed\Message\Message
     * @uses WellRESTed\Message\HeaderCollection
     */
    public function testWithoutAttributePreservesOtherAttributes()
    {
        $request = new ServerRequest();
        $request = $request->withAttribute("cat", "Molly");
        $request = $request->withAttribute("dog", "Bear");
        $request = $request->withoutAttribute("cat");
        $this->assertEquals("Bear", $request->getAttribute("dog"));
        $this->assertEquals("Oscar", $request->getAttribute("cat", "Oscar"));
    }

    /**
     * @covers WellRESTed\Message\ServerRequest::getAttributes
     * @uses WellRESTed\Message\ServerRequest::withAttribute
     * @uses WellRESTed\Message\ServerRequest::__construct
     * @uses WellRESTed\Message\ServerRequest::__clone
     * @uses WellRESTed\Message\ServerRequest::withParsedBody
     * @uses WellRESTed\Message\ServerRequest::getParsedBody
     * @uses WellRESTed\Message\Request
     * @uses WellRESTed\Message\Message
     * @uses WellRESTed\Message\HeaderCollection
     */
    public function testGetAttributesReturnsAllAttributes()
    {
        $request = new ServerRequest();
        $request = $request->withAttribute("cat", "Molly");
        $request = $request->withAttribute("dog", "Bear");
        $attributes = $request->getAttributes();
        $this->assertEquals("Molly", $attributes["cat"]);
        $this->assertEquals("Bear", $attributes["dog"]);
    }
}