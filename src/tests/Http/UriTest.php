<?php

namespace Alpha\tests\Http;

use Alpha\Http\Uri;
use PHPUnit\Framework\TestCase;

class UriTest extends TestCase
{
    /**
     * @covers Uri::getScheme
     */
    public function testGetScheme()
    {
        $uri = new Uri('https', 'example.com');
        $this->assertEquals('https', $uri->getScheme());
    }

    /**
     * @covers Uri::getAuthority
     */
    public function testGetAuthority()
    {
        $uri = new Uri('https', 'example.com', 8080, '/path');
        $this->assertEquals('example.com:8080', $uri->getAuthority());
    }

    /**
     * @covers Uri::getUserInfo
     */
    public function testGetUserInfo()
    {
        $uri = new Uri('https', 'example.com', null, '/path', '', '', 'user:pass');
        $this->assertEquals('user:pass', $uri->getUserInfo());
    }

    /**
     * @covers Uri::getHost
     */
    public function testGetHost()
    {
        $uri = new Uri('https', 'example.com');
        $this->assertEquals('example.com', $uri->getHost());
    }

    /**
     * @covers Uri::getPort
     */
    public function testGetPort()
    {
        $uri = new Uri('https', 'example.com', 8080);
        $this->assertEquals(8080, $uri->getPort());
    }

    /**
     * @covers Uri::getPath
     */
    public function testGetPath()
    {
        $uri = new Uri('https', 'example.com', null, '/path');
        $this->assertEquals('/path', $uri->getPath());
    }

    /**
     * @covers Uri::getQuery
     */
    public function testGetQuery()
    {
        $uri = new Uri('https', 'example.com', null, '/path', 'key=value');
        $this->assertEquals('key=value', $uri->getQuery());
    }

    /**
     * @covers Uri::getFragment
     */
    public function testGetFragment()
    {
        $uri = new Uri('https', 'example.com', null, '/path', '', 'fragment');
        $this->assertEquals('fragment', $uri->getFragment());
    }

    /**
     * @covers Uri::withScheme
     */
    public function testWithScheme()
    {
        $uri = new Uri('https', 'example.com');
        $newUri = $uri->withScheme('http');
        $this->assertEquals('http', $newUri->getScheme());
    }

    /**
     * @covers Uri::withUserInfo
     */
    public function testWithUserInfo()
    {
        $uri = new Uri('https', 'example.com');
        $newUri = $uri->withUserInfo('user', 'pass');
        $this->assertEquals('user:pass', $newUri->getUserInfo());
    }

    /**
     * @covers Uri::withHost
     */
    public function testWithHost()
    {
        $uri = new Uri('https', 'example.com');
        $newUri = $uri->withHost('newexample.com');
        $this->assertEquals('newexample.com', $newUri->getHost());
    }

    /**
     * @covers Uri::withPort
     */
    public function testWithPort()
    {
        $uri = new Uri('https', 'example.com');
        $newUri = $uri->withPort(8080);
        $this->assertEquals(8080, $newUri->getPort());
    }

    /**
     * @covers Uri::withPath
     */
    public function testWithPath()
    {
        $uri = new Uri('https', 'example.com');
        $newUri = $uri->withPath('/newpath');
        $this->assertEquals('/newpath', $newUri->getPath());
    }

    /**
     * @covers Uri::withQuery
     */
    public function testWithQuery()
    {
        $uri = new Uri('https', 'example.com');
        $newUri = $uri->withQuery('key=value');
        $this->assertEquals('key=value', $newUri->getQuery());
    }

    /**
     * @covers Uri::withFragment
     */
    public function testWithFragment()
    {
        $uri = new Uri('https', 'example.com');
        $newUri = $uri->withFragment('newfragment');
        $this->assertEquals('newfragment', $newUri->getFragment());
    }
}