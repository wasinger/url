url
===

PHP class for handling and manipulating URLs. It's a pragmatic one-class lib that is completely framework independent.

[![Build Status](https://secure.travis-ci.org/wasinger/url.svg?branch=master)](http://travis-ci.org/wasinger/url)
[![Latest Version](http://img.shields.io/packagist/v/wa72/url.svg)](https://packagist.org/packages/wa72/url)


- Parse URL strings to objects
- add and modify query parameters
- set and modify any part of the url
- test for equality of URLs with query parameters in a PHP-fashioned way
- supports protocol-relative urls
- convert absolute, host-relative and protocol-relative urls to relative and vice versa

- New in version 0.7 (2018/07/25): optional compatibility with  `Psr\Http\Message\UriInterface` (PSR-7), see below

Installation
------------

This package is listed on [Packagist](https://packagist.org/packages/wa72/url).

```
composer require wa72/url
```

Features and Usage
------------------

### Parse a URL to an object ###

```php
use \Wa72\Url;

$url = new Url('http://my-server.com/index.php?p1=foo&p2=bar');
// or alternatively use the static factory function `parse`:
$url = Url::parse('http://my-server.com/index.php?p1=foo&p2=bar');

// set another host
$url->setHost('another-server.org');

// return the URL as string again
echo $url->write();
// or simply:
echo $url;
```

### Easily modify and add query parameters ###

```php
$url->setQueryParameter('p1', 'newvalue');
$url->setQueryParameter('param3', 'another value');
echo $url;
// will output:
// http://another-server.org/index.php?p1=newvalue&p2=bar&param3=another%20value

// You can even add arrays a query parameter:
$url->setQueryParameter('param3', array(5, 6));
echo $url;
// will output:
// http://another-server.org/index.php?p1=newvalue&p2=bar&param3[]=5&param3[]=6
```

### Compare URLs with query strings the PHP way ###

While in general a URL may have multiple query parameters with the same name 
(e.g. `?a=value1&a=value2&a=value3`) and there are web applications that convert
those parameters into an array, this is not the PHP way of dealing with query parameters:
In PHP, the last parameter with the same name always wins, so the 
above query string is equal to only `?a=value3`.

Similarly, while in general the order of query parameters in the query string may
be significant to a web application, it is not in PHP: `?a=1&b=2` is equivalent 
to `?b=2&a=1` for a PHP application.

`Url` deals with query strings in URLs like PHP does, so the URLs in the following
example are to be considered equal:

```php
$url1 = Url::parse('index.php?a=0&a=1&b=2');
$url2 = Url::parse('index.php?b=2&a=1');

return $url1.equals($url2);
// will return TRUE
```

### Make relative URL absolute ###

A given URL that has 

- no scheme (protocol-relative URL)
- no scheme and no host (host-relative URL)
- no scheme, no host, and a relative path (relative URL)

can be turned into an absolute URL by a given base URL:

```php
$url = Url::parse('page.php');
$baseurl = Url::parse('http://www.test.test/index.html');
$url->makeAbsolute($baseurl);
echo $url; // will print: http://www.test.test/page.php

$url = Url::parse('../de/seite.html');
$baseurl = Url::parse('http://www.test.test/en/page.html');
$url->makeAbsolute($baseurl);
echo $url; // will print: http://www.test.test/de/seite.html

$url = Url::parse('/index.html');
$baseurl = Url::parse('http://www.test.test/en/page.html');
$url->makeAbsolute($baseurl);
echo $url; // will print: http://www.test.test/index.html

$url = Url::parse('/index.html');
$baseurl = Url::parse('http://www.test.test/en/page.html');
$url->makeAbsolute($baseurl);
echo $url; // will print: http://www.test.test/index.html

$url = Url::parse('//www.test.test/index.html');
$baseurl = Url::parse('https://www.test.test/en/page.html');
$url->makeAbsolute($baseurl);
echo $url; // will print: https://www.test.test/index.html
```

### Output protocol-relative and host-relative URLs ###

If you want to omit the scheme, or scheme and host, when outputting the URL 
you can pass `Url::WRITE_FLAG_OMIT_SCHEME` and with `Url::WRITE_FLAG_OMIT_HOST` to the `write()`-method:

```php
$url = Url::parse('https://www.test.test/index.php?id=5#c1');

// protocol-relative output
echo $url->write(Url::WRITE_FLAG_OMIT_SCHEME); // will print: //www.test.test/index.php?id=5#c1

// host-relative output
echo $url->write(Url::WRITE_FLAG_OMIT_SCHEME | Url::WRITE_FLAG_OMIT_HOST)); // will print: /index.php?id=5#c1
```

### Compatibility with `Psr\Http\Message\UriInterface` (PSR-7) ###


- class `Url` now has all methods defined in this interface but does not officially implement it.
- new wrapper class `Psr7Uri` that implements `UriInterface`
- methods for converting between `Url` and `Psr7Uri`

Class `Url` does not implement the PSR Interface by itself for two reasons:
1. To not introduce a new dependency on the PSR interface. The dependency is only "suggested" in composer json.
2. Because the PSR interface is designed to be immutable,
    while `Url` is not.

To use this feature, you need to `composer require psr/http-message`

```php
<?php
use Wa72\Url\Psr7Uri;
use Wa72\Url\Url;

# Get a Psr7Uri from a Url object

$url = Url::parse('https://www.foo.bar/test.php?a=b');
$psr7uri = Psr7Uri::fromUrl($url);
// or alternatively:
$psr7uri = $url->toPsr7();

# Get a Url object from UriInterface

$url = Url::fromPsr7($psr7uri);
// or alternatively:
$url = $psr7uri->toUrl();


```

### More documentation to come ###

Meanwhile, have a look at the source code, there are lots of comments in it.

(c) Christoph Singer 2018. Licensed under the MIT license.
