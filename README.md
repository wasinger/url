url
===

PHP class for handling and manipulating URLs. It's a pragmatic one-class lib that is completely framework independent.

[![Build Status](https://secure.travis-ci.org/wasinger/url.png?branch=master)](http://travis-ci.org/wasinger/url)

- Parse URL strings to objects
- add and modify query parameters
- set and modify any part of the url
- test for equality of URLs with query parameters in a PHP-fashioned way
- supports protocol-relative urls
- convert absolute, host-relative and protocol-relative urls to relative and vice versa

Installation
------------
```
composer require "wa72/url": "dev-master"
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

### More documentation to come ###

Meanwhile, have a look at the source code, there are lots of comments in it.

(c) Christoph Singer 2015. Licensed under the MIT license.
