<?php
namespace Wa72\Url\Tests;

use Wa72\Url\Url;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase {

    /**
     * @covers \Wa72\Url\Url::equalsQuery()
     */
    public function testEqualsQuery()
    {
        $url1 = Url::parse('index.php?a=3&b=5');
        $url2 = new Url('index.php?b=5&a=3');
        $this->assertTrue($url1->equalsQuery($url2));

        // Queries are considered to be equal if they parse to the same array in PHP!!!
        // If a key is given more than once with different values, the last one wins.
        $url1 = Url::parse('index.php?a=1&a=3&b=5');
        $url2 = new Url('index.php?b=2&b=5&a=3');
        $this->assertTrue($url1->equalsQuery($url2));

        $url1 = new Url('index.php?a=3&b=5&c=asdf');
        $url2 = Url::parse('index.php?b=5&a=3');
        $this->assertFalse($url1->equalsQuery($url2));

        $url1 = new Url('index.php?a=3&b=5&c=asdf');
        $queryString = 'index.php?b=5&a=3';
        $this->assertFalse($url1->equalsQuery($queryString));
    }

    /**
     * @covers \Wa72\Url\Url::equalsHost()
     */
    public function testEqualsHost()
    {
        $url = new Url('http://example.com');
        $this->assertTrue($url->equalsHost('example.com'));
        $this->assertFalse($url->equalsHost('example.com.tw'));
    }

    /**
     * @covers \Wa72\Url\Url::filename()
     */
    public function testFilename()
    {
        $this->assertEquals('file.php', Url::filename('/path/to/file.php'));
    }

    /**
     * @covers \Wa72\Url\Url::dirname()
     */
    public function testDirname()
    {
        $this->assertEquals('/', Url::dirname('/dirname'));
    }

    /**
     * @covers \Wa72\Url\Url::parse()
     */
    public function testParse()
    {
        $this->assertEquals('example.com', Url::parse('http://example.com')->getHost());
    }

    /**
     * @covers \Wa72\Url\Url::__construct()
     * @covers \Wa72\Url\Url::is_url()
     */
    public function testConstructor()
    {
        $url = new Url('http://example.com?param=value');
        $this->assertInstanceOf('\Wa72\Url\Url', $url);
        $this->assertTrue($url->is_url());
    }

    /**
     * @covers \Wa72\Url\Url::__construct()
     * @covers \Wa72\Url\Url::is_url()
     */
    public function testConstructorOnRelativeProtocol()
    {
        $url = new Url('//example.com?param=value');
        $this->assertInstanceOf('\Wa72\Url\Url', $url);
        $this->assertTrue($url->is_url());    
    }

    public function isUrlProvider()
    {
        return [
            ['http://example.com'],
            ['https://example.com'],
            ['ftp://ftp.example.com'],
            ['ftps://ftps.example.com'],
            ['file:///path/to/file'],
        ];
    }

    /**
     * @covers \Wa72\Url\Url::__construct()
     * @covers \Wa72\Url\Url::is_url()
     * @dataProvider isUrlProvider
     */
    public function testIsUrl($link)
    {
        $url = new Url($link);
        $this->assertTrue($url->is_url());
    }

    /**
     * @covers \Wa72\Url\Url::is_local()
     */
    public function testIsLocal()
    {
        $url = new Url('#local_hash_tag');
        $this->assertTrue($url->is_local());
    }

    /**
     * @covers \Wa72\Url\Url::is_relative()
     */
    public function testIsRelative()
    {
        $url = new Url('relative_path');
        $this->assertTrue($url->is_relative());
    }

    /**
     * @covers \Wa72\Url\Url::is_host_relative()
     */
    public function testIsHostRelative()
    {
        $url = new Url('/relative/path');
        $this->assertTrue($url->is_host_relative());
    }

    /**
     * @covers \Wa72\Url\Url::is_absolute()
     */
    public function testIsAbsolute()
    {
        $url = new Url('http://example.com');
        $this->assertTrue($url->is_absolute());
    }
 
    /**
     * @covers \Wa72\Url\Url::is_protocol_relative()
     */
    public function testIsProtocolRelative()
    {
        $url = new Url('//example.com/');
        $this->assertTrue($url->is_protocol_relative());
    }

    /**
     * @covers \Wa72\Url\Url::__toString()
     * @covers \Wa72\Url\Url::write()
     */
    public function testToString()
    {
        $url = new Url('ssh://user@example.com:2222');
        $this->assertEquals('ssh://user@example.com:2222', (string) $url);
    }

    /**
     * @covers \Wa72\Url\Url::setFragment()
     */
    public function testSetFragment()
    {
        $url = new Url('//example.com/');
        $url->setFragment('/');
        $this->assertEquals('//example.com/#/', (string) $url);
    }

    /**
     * @covers \Wa72\Url\Url::getFragment()
     */
    public function testGetFragment()
    {
        $url = new Url('//example.com');
        $url->setFragment('/');
        $this->assertEquals('/', $url->getFragment());
    }

    /**
     * @covers \Wa72\Url\Url::setHost()
     */
    public function testSetHost()
    {
        $url = new Url('//example.com');
        $url->setHost('example2.com');
        $this->assertEquals('//example2.com', (string) $url);
    }

    /**
     * @covers \Wa72\Url\Url::getHost()
     */
    public function testGetHost()
    {
        $url = new Url('//example.com');
        $url->setHost('example2.com');
        $this->assertEquals('example2.com', $url->getHost());
    }

    /**
     * @covers \Wa72\Url\Url::setPass()
     */
    public function testSetPass()
    {
        $url = new Url('http://username:password@www.my_site.com');
        $url->setPass('passWord');
        $this->assertEquals('http://username:passWord@www.my_site.com', (string) $url);
    }

    /**
     * @covers \Wa72\Url\Url::getPass()
     */
    public function testGetPass()
    {
        $url = new Url('http://username:password@www.my_site.com');
        $url->setPass('passWord');
        $this->assertEquals('passWord', $url->getPass());
    }

    /**
     * @covers \Wa72\Url\Url::setPath()
     */
    public function testSetPath()
    {
        $url = new Url('http://www.my_site.com');
        $url->setPath('/path');
        $this->assertEquals('http://www.my_site.com/path', (string) $url);
    }

    /**
     * @covers \Wa72\Url\Url::getPath()
     */
    public function testGetPath()
    {
        $url = new Url('http://www.my_site.com');
        $url->setPath('/path');
        $this->assertEquals('/path', $url->getPath());
    }

    /**
     * @covers \Wa72\Url\Url::setPort()
     */
    public function testSetPort()
    {
        $url = new Url('http://www.my_site.com:8000');
        $url->setPort(5000);
        $this->assertEquals('http://www.my_site.com:5000', (string) $url);
    }

    /**
     * @covers \Wa72\Url\Url::getPort()
     */
    public function testGetPort()
    {
        $url = new Url('http://www.my_site.com:8000');
        $url->setPort(5000);
        $this->assertEquals(5000, $url->getPort());
    }

    /**
     * @covers \Wa72\Url\Url::setQuery()
     */
    public function testSetQuery()
    {
        $url = new Url('http://www.my_site.com?param=value');
        $url->setQuery('param2=value2');
        $this->assertEquals('http://www.my_site.com?param2=value2', (string) $url);
    }

    /**
     * @covers \Wa72\Url\Url::getQuery()
     */
    public function testGetQuery()
    {
        $url = new Url('http://www.my_site.com?param=value');
        $url->setQuery('param2=value2');
        $this->assertEquals('param2=value2', $url->getQuery());
    }

    /**
     * @covers \Wa72\Url\Url::setScheme()
     */
    public function testSetScheme()
    {
        $url = new Url('HTTP://www.my_site.com');
        $url->setScheme('SFTP');
        $this->assertEquals('sftp://www.my_site.com', (string) $url);
    }

    /**
     * @covers \Wa72\Url\Url::getScheme()
     */
    public function testGetScheme()
    {
        $url = new Url('HTTP://www.my_site.com');
        $url->setScheme('SFTP');
        $this->assertEquals('sftp', $url->getScheme());
    }

    /**
     * @covers \Wa72\Url\Url::setUser()
     */
    public function testSetUser()
    {
        $url = new Url('http://username:passwod@www.my_site.com');
        $url->setUser('user_name');
        $this->assertEquals('http://user_name:passwod@www.my_site.com', (string) $url);
    }

    /**
     * @covers \Wa72\Url\Url::getUser()
     */
    public function testGetUser()
    {
        $url = new Url('http://username:passwod@www.my_site.com');
        $url->setUser('user_name');
        $this->assertEquals('user_name', $url->getUser());
    }

    /**
     * @covers \Wa72\Url\Url::hasQueryParameter()
     */
    public function testHasQueryParameter()
    {
        $url = new Url('http://www.my_site.com?param=value');
        $this->assertTrue($url->hasQueryParameter('param'));
        $this->assertFalse($url->hasQueryParameter('invalid_param'));
    }

    /**
     * @covers \Wa72\Url\Url::getQueryParameter()
     */
    public function testGetQueryParameter()
    {
        $url = new Url('http://www.my_site.com?param=value');
        $this->assertEquals('value', $url->getQueryParameter('param'));
        $this->assertNull($url->getQueryParameter('invalid_param'));
    }

    /**
     * @covers \Wa72\Url\Url::setQueryParameter()
     */
    public function testSetQueryParameter()
    {
        $url = new Url('http://www.my_site.com?param=value');
        $url->setQueryParameter('param2', 'value2');
        $this->assertEquals('value2', $url->getQueryParameter('param2'));
        $this->assertNull($url->getQueryParameter('param3'));
    }

    /**
     * @covers \Wa72\Url\Url::setQueryFromArray()
     */
    public function testSetQueryFromArray()
    {
        $url = new Url('http://www.my_site.com?param=value');
        $url->setQueryFromArray([
            'param2' => 'value2',
            'param3' => 'value3',
        ]);
        $this->assertEquals('value2', $url->getQueryParameter('param2'));
        $this->assertEquals('value3', $url->getQueryParameter('param3'));
        $this->assertNull($url->getQueryParameter('param4'));
    }

    /**
     * @covers \Wa72\Url\Url::getQueryArray()
     */
    public function testGetQueryArray()
    {
        $url = new Url('http://www.my_site.com?param=value');
        $url->setQueryFromArray([
            'param2' => 'value2',
            'param3' => 'value3',
        ]);
        $this->assertEquals([
            'param2' => 'value2',
            'param3' => 'value3',
        ], $url->getQueryArray());
    }

    /**
     * @covers \Wa72\Url\Url::equals()
     */
    public function testEquals()
    {
        $url = new Url('http://www.example.com');
        $this->assertTrue($url->equals('http://www.example.com'));
        $this->assertFalse($url->equals('www.example.com'));
    }

    /**
     * @covers \Wa72\Url\Url::equalsPath()
     */
    public function testEqualsPath()
    {
        $url = new Url('http://www.example.com/path');
        $this->assertFalse($url->equalsPath('/path/to'));
        $this->assertTrue($url->equalsPath('/path'));
    }

    /**
     * @covers \Wa72\Url\Url::makeAbsolute()
     */
    public function testMakeAbsolute()
    {
        $url1 = new Url('page.php?a=3&b=5');
        $url2 = Url::parse('http://www.test.test/index.html');

        $this->assertEquals('http://www.test.test/page.php?a=3&b=5', (string) $url1->makeAbsolute($url2));

        $url1 = new Url('/bar.html');
        $this->assertEquals('http://www.test.test/bar.html', (string) $url1->makeAbsolute($url2));

        $url1 = new Url('//xy.bar.com/index.html');
        $this->assertEquals('http://xy.bar.com/index.html', (string) $url1->makeAbsolute($url2));
    }

    /**
     * @covers \Wa72\Url\Url::buildAbsolutePath()
     */
    public function testBuildAbsolutePath()
    {
        $p1 = 'page2.html';
        $p2 = '/pages/index.html';
        $this->assertEquals('/pages/page2.html', Url::buildAbsolutePath($p1, $p2));

        $p1 = 'page.php';
        $p2 = '/index.html';
        $this->assertEquals('/page.php', Url::buildAbsolutePath($p1, $p2));

        $p1 = 'a/b';
        $p2 = '/c/d/e';
        $this->assertEquals('/c/d/a/b', Url::buildAbsolutePath($p1, $p2));

        $p1 = '../images/1.gif';
        $p2 = '/pages/index.html';
        $this->assertEquals('/images/1.gif', Url::buildAbsolutePath($p1, $p2));

        $p1 = '../images/1.gif';
        $p2 = '/pages/';
        $this->assertEquals('/images/1.gif', Url::buildAbsolutePath($p1, $p2));

        $p1 = 'images/1.gif';
        $p2 = '/index.html';
        $this->assertEquals('/images/1.gif', Url::buildAbsolutePath($p1, $p2));

        $p1 = 'images/1.gif';
        $p2 = '/';
        $this->assertEquals('/images/1.gif', Url::buildAbsolutePath($p1, $p2));

        $p1 = '/a/b';
        $p2 = '/c/d/e';
        $this->assertEquals('/a/b', Url::buildAbsolutePath($p1, $p2));
    }

    /**
     * @covers \Wa72\Url\Url::normalizePath()
     */
    public function testNormalizePath()
    {
        $this->assertEquals('foo/bar', Url::normalizePath('./foo/bar'));
        $this->assertEquals('foo/bar', Url::normalizePath('foo/./bar'));
        $this->assertEquals('foo/bar', Url::normalizePath('foo/foo/../bar'));
        $this->assertEquals('foo/bar', Url::normalizePath('foo/asdf/qwer/../../bar'));
        $this->assertEquals('../bar', Url::normalizePath('../bar'));
    }

    /**
     * @covers \Wa72\Url\Url::getFilename()
     */
    public function testGetFilename()
    {
        $url = Url::parse('/asdf/index.html');
        $this->assertEquals('index.html', $url->getFilename());

        $url = Url::parse('/asdf/');
        $this->assertEquals('', $url->getFilename());

        $url = Url::parse('/foo');
        $this->assertEquals('foo', $url->getFilename());

        $url = Url::parse('foo');
        $this->assertEquals('foo', $url->getFilename());

        $url = Url::parse('foo/');
        $this->assertEquals('', $url->getFilename());
    }
    /**
     * @covers \Wa72\Url\Url::getDirname()
     */
    public function testGetDirname()
    {
        $url = Url::parse('/asdf/index.html');
        $this->assertEquals('/asdf', $url->getDirname());

        $url = Url::parse('/asdf/');
        $this->assertEquals('/asdf', $url->getDirname());

        $url = Url::parse('/foo');
        $this->assertEquals('/', $url->getDirname());

        $url = Url::parse('foo');
        $this->assertEquals('.', $url->getDirname());

        $url = Url::parse('foo/');
        $this->assertEquals('foo', $url->getDirname());
    }
    /**
     * @covers \Wa72\Url\Url::appendPathSegment()
     */
    public function testAppendPathSegment()
    {
        $url = Url::parse('foo');
        $url->appendPathSegment('bar');
        $this->assertEquals('foo/bar', $url->__toString());

        $url = Url::parse('foo/');
        $url->appendPathSegment('/bar');
        $this->assertEquals('foo/bar', $url->__toString());

        $url = Url::parse('/foo');
        $url->appendPathSegment('/bar');
        $this->assertEquals('/foo/bar', $url->__toString());

        $url = Url::parse('http://www.test.test');
        $url->appendPathSegment('index.php');
        $this->assertEquals('http://www.test.test/index.php', $url->__toString());

    }
    /**
     * @covers \Wa72\Url\Url::write()
     */
    public function testWrite()
    {
        $u = 'http://user:password@host.com/foo/bar?asdf=qwer&zui=hjk#asdf';
        $url = Url::parse($u);
        $this->assertEquals($u, $url->write());

        // Test: omit standard port
        $u1 = 'http://user:password@host.com:80/foo/bar?asdf=qwer&zui=hjk#asdf';
        $u2 = 'http://user:password@host.com/foo/bar?asdf=qwer&zui=hjk#asdf';
        $url = Url::parse($u1);
        $this->assertEquals($u2, $url->write());

        // Test: non-standard port
        $u = 'http://user:password@host.com:81/foo/bar?asdf=qwer&zui=hjk#asdf';
        $url = Url::parse($u);
        $this->assertEquals($u, $url->write());

        $u = 'http://user@host/foo/bar?asdf=qwer&zui=hjk#asdf';
        $url = Url::parse($u);
        $this->assertEquals($u, $url->write());
        $this->assertEquals('//user@host/foo/bar?asdf=qwer&zui=hjk#asdf',
            $url->write(Url::WRITE_FLAG_OMIT_SCHEME));
        $this->assertEquals('/foo/bar?asdf=qwer&zui=hjk#asdf',
            $url->write(Url::WRITE_FLAG_OMIT_SCHEME | Url::WRITE_FLAG_OMIT_HOST));

        $u = 'http://www.test.test';
        $url = Url::parse($u);
        $this->assertEquals($u, $url->write());

        $u = 'http://www.test.test/index.html';
        $url = Url::parse($u);
        $this->assertEquals($u, $url->write());

        $u = '../index.php?foo=bar';
        $url = Url::parse($u);
        $this->assertEquals($u, $url->write());

        $u = '#asdf';
        $url = Url::parse($u);
        $this->assertEquals($u, $url->write());
    }

    /**
     * @covers \Wa72\Url\Url::isInPath()
     */
    public function testIsInPath()
    {
        $url = Url::parse('/foo/bar.html');
        $this->assertTrue($url->isInPath('/foo/'));
        $this->assertTrue($url->isInPath('/foo'));
        $this->assertFalse($url->isInPath('/foo/bar'));

        $url = Url::parse('/de');
        $this->assertTrue($url->isInPath('/de'));
        $this->assertFalse($url->isInPath('/de/'));

        $url = Url::parse('/de/');
        $this->assertTrue($url->isInPath('/de'));
        $this->assertTrue($url->isInPath('/de/'));

    }
}

