<?php

namespace pjdietz\WellRESTed\Test;

use Faker\Factory;
use pjdietz\ShamServer\ShamServer;
use pjdietz\WellRESTed\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider httpMethodProvider
     */
    public function testSendHttpMethod($method)
    {
        $host = "localhost";
        $port = 8080;
        $script = realpath(__DIR__ . "/sham-routers/method.php");

        $server = new ShamServer($host, $port, $script);

        $rqst = $this->getMockBuilder('pjdietz\WellRESTed\Interfaces\RequestInterface')->getMock();
        $rqst->expects($this->any())
            ->method("getUri")
            ->will($this->returnValue("http://$host:$port"));
        $rqst->expects($this->any())
            ->method("getMethod")
            ->will($this->returnValue($method));
        $rqst->expects($this->any())
            ->method("getPort")
            ->will($this->returnValue($port));
        $rqst->expects($this->any())
            ->method("getHeaders")
            ->will($this->returnValue(array()));

        $client = new Client();
        $resp = $client->request($rqst);
        $body = trim($resp->getBody());
        $this->assertEquals($method, $body);

        $server->stop();
    }

    public function httpMethodProvider()
    {
        return [
            ["GET"],
            ["POST"],
            ["PUT"],
            ["DELETE"],
            ["PATCH"],
            ["OPTIONS"]
        ];
    }

    /**
     * @dataProvider httpHeaderProvider
     */
    public function testSendHttpHeaders($headerKey, $headerValue)
    {
        $host = "localhost";
        $port = 8080;
        $script = realpath(__DIR__ . "/sham-routers/headers.php");

        $server = new ShamServer($host, $port, $script);

        $rqst = $this->getMockBuilder('pjdietz\WellRESTed\Interfaces\RequestInterface')->getMock();
        $rqst->expects($this->any())
            ->method("getUri")
            ->will($this->returnValue("http://$host:$port"));
        $rqst->expects($this->any())
            ->method("getMethod")
            ->will($this->returnValue("GET"));
        $rqst->expects($this->any())
            ->method("getPort")
            ->will($this->returnValue($port));
        $rqst->expects($this->any())
            ->method("getHeaders")
            ->will($this->returnValue(array($headerKey => $headerValue)));

        $client = new Client();
        $resp = $client->request($rqst);
        $headers = json_decode($resp->getBody());
        $this->assertEquals($headerValue, $headers->{$headerKey});

        $server->stop();
    }

    public function httpHeaderProvider()
    {
        return [
            ["Cache-Control", "max-age=0"],
            ["X-Custom-Header", "custom value"],
            ["Accept-Charset", "utf-8"]
        ];
    }

    /**
     * @dataProvider bodyProvider
     */
    public function testSendBody($body)
    {
        $host = "localhost";
        $port = 8080;
        $script = realpath(__DIR__ . "/sham-routers/body.php");
        $server = new ShamServer($host, $port, $script);

        $rqst = $this->getMockBuilder('pjdietz\WellRESTed\Interfaces\RequestInterface')->getMock();
        $rqst->expects($this->any())
            ->method("getUri")
            ->will($this->returnValue("http://$host:$port"));
        $rqst->expects($this->any())
            ->method("getMethod")
            ->will($this->returnValue("POST"));
        $rqst->expects($this->any())
            ->method("getPort")
            ->will($this->returnValue($port));
        $rqst->expects($this->any())
            ->method("getHeaders")
            ->will($this->returnValue(array()));
        $rqst->expects($this->any())
            ->method("getBody")
            ->will($this->returnValue($body));

        $client = new Client();
        $resp = $client->request($rqst);
        $this->assertEquals($body, $resp->getBody());
        $server->stop();
    }

    public function bodyProvider()
    {
        $faker = Factory::create();
        return [
            [$faker->text()],
            [$faker->text()],
            [$faker->text()]
        ];
    }

    public function testSetCustomCurlOptionsOnInstantiation()
    {
        $host = "localhost";
        $port = 8080;
        $script = realpath(__DIR__ . "/sham-routers/headers.php");
        $server = new ShamServer($host, $port, $script);

        $rqst = $this->getMockBuilder('pjdietz\WellRESTed\Interfaces\RequestInterface')->getMock();
        $rqst->expects($this->any())
            ->method("getUri")
            ->will($this->returnValue("http://$host:$port"));
        $rqst->expects($this->any())
            ->method("getMethod")
            ->will($this->returnValue("GET"));
        $rqst->expects($this->any())
            ->method("getPort")
            ->will($this->returnValue($port));
        $rqst->expects($this->any())
            ->method("getHeaders")
            ->will($this->returnValue(array()));

        $cookieValue = "key=value";
        $client = new Client([CURLOPT_COOKIE => $cookieValue]);
        $resp = $client->request($rqst);
        $headers = json_decode($resp->getBody());
        $this->assertEquals($cookieValue, $headers->Cookie);

        $server->stop();
    }

    public function testSetCustomCurlOptionsOnRequest()
    {
        $host = "localhost";
        $port = 8080;
        $script = realpath(__DIR__ . "/sham-routers/headers.php");
        $server = new ShamServer($host, $port, $script);

        $rqst = $this->getMockBuilder('pjdietz\WellRESTed\Interfaces\RequestInterface')->getMock();
        $rqst->expects($this->any())
            ->method("getUri")
            ->will($this->returnValue("http://$host:$port"));
        $rqst->expects($this->any())
            ->method("getMethod")
            ->will($this->returnValue("GET"));
        $rqst->expects($this->any())
            ->method("getPort")
            ->will($this->returnValue($port));
        $rqst->expects($this->any())
            ->method("getHeaders")
            ->will($this->returnValue(array()));

        $cookieValue = "key=value";
        $client = new Client();
        $resp = $client->request($rqst, [CURLOPT_COOKIE => $cookieValue]);
        $headers = json_decode($resp->getBody());
        $this->assertEquals($cookieValue, $headers->Cookie);

        $server->stop();
    }

}
