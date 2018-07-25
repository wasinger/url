<?php
namespace Wa72\Url\Tests;

use Psr\Http\Message\UriInterface;
use Wa72\Url\Psr7Uri;
use Wa72\Url\Url;
use PHPUnit\Framework\TestCase;

class Psr7UriTest extends TestCase
{
    public function testToUrl()
    {
        $psr7uri = Psr7Uri::parse('http://www.test.test/index.html');
        $url = $psr7uri->toUrl();
        $this->assertInstanceOf(Url::class, $url);
        $this->assertEquals('http://www.test.test/index.html', (string) $url);
    }

    public function testGetUserInfo()
    {
        $psr7uri = Psr7Uri::parse('http://user:pass@www.test.test/index.html');
        $this->assertEquals('user:pass', $psr7uri->getUserInfo());
    }

    public function testGetPort()
    {
        $psr7uri = Psr7Uri::parse('http://user:pass@www.test.test/index.html');
        $this->assertNull($psr7uri->getPort());

        $psr7uri = Psr7Uri::parse('http://user:pass@www.test.test:81/index.html');
        $this->assertEquals(81, $psr7uri->getPort());
    }

    public function testGetAuthority()
    {
        $psr7uri = Psr7Uri::parse('http://user:pass@www.test.test/index.html');
        $this->assertEquals('user:pass@www.test.test', $psr7uri->getAuthority());
    }

    public function testWithScheme()
    {
        $psr7uri = Psr7Uri::parse('http://user:pass@www.test.test/index.html');
        $uri2 = $psr7uri->withScheme('https');
        $this->assertEquals('https://user:pass@www.test.test/index.html', (string) $uri2);
    }

    public function testWithFragment()
    {
        $psr7uri = Psr7Uri::parse('http://user:pass@www.test.test/index.html');
        $uri2 = $psr7uri->withFragment('foo');
        $this->assertEquals('http://user:pass@www.test.test/index.html#foo', (string) $uri2);
    }

    public function testGetQuery()
    {
        $psr7uri = Psr7Uri::parse('http://test.test/index.php?a=b');
        $this->assertEquals('a=b', $psr7uri->getQuery());
    }

    public function testGetFragment()
    {
        $psr7uri = Psr7Uri::parse('http://test.test/index.php#foo');
        $this->assertEquals('foo', $psr7uri->getFragment());
    }

    public function testGetPath()
    {
        $psr7uri = Psr7Uri::parse('http://test.test/index.php#foo');
        $this->assertEquals('/index.php', $psr7uri->getPath());
    }

    public function testGetScheme()
    {
        $psr7uri = Psr7Uri::parse('http://test.test/index.php#foo');
        $this->assertEquals('http', $psr7uri->getScheme());
    }

    public function testGetHost()
    {
        $psr7uri = Psr7Uri::parse('http://test.test/index.php#foo');
        $this->assertEquals('test.test', $psr7uri->getHost());
    }

    public function testWithPort()
    {
        $psr7uri = Psr7Uri::parse('http://user:pass@www.test.test/index.html');
        $uri2 = $psr7uri->withPort(81);
        $this->assertEquals('http://user:pass@www.test.test:81/index.html', (string) $uri2);
    }

    public function testWithHost()
    {
        $psr7uri = Psr7Uri::parse('http://user:pass@www.test.test/index.html');
        $uri2 = $psr7uri->withHost('another-host');
        $this->assertEquals('http://user:pass@another-host/index.html', (string) $uri2);
    }

    public function testWithPath()
    {
        $psr7uri = Psr7Uri::parse('http://user:pass@www.test.test/index.html');
        $uri2 = $psr7uri->withPath('/a/b/test.html');
        $this->assertEquals('http://user:pass@www.test.test/a/b/test.html', (string) $uri2);
    }

    public function testWithUserInfo()
    {
        $psr7uri = Psr7Uri::parse('http://user:pass@www.test.test/index.html');
        $uri2 = $psr7uri->withUserInfo('u2', 'secret');
        $this->assertEquals('http://u2:secret@www.test.test/index.html', (string) $uri2);
    }

    public function testWithQuery()
    {
        $psr7uri = Psr7Uri::parse('http://user:pass@www.test.test/index.html');
        $uri2 = $psr7uri->withQuery('a=b');
        $this->assertEquals('http://user:pass@www.test.test/index.html?a=b', (string) $uri2);
    }

    public function testFromUrl()
    {
        $url = Url::parse('https://www.foo.bar/test.php?a=b');
        $psr7uri = Psr7Uri::fromUrl($url);
        $this->assertInstanceOf(UriInterface::class, $psr7uri);
        $this->assertEquals('https://www.foo.bar/test.php?a=b', (string) $psr7uri);
    }
}
