<?php
namespace Wa72\Url;

class Url
{
    protected $original_url;
    protected $scheme;
    protected $user;
    protected $pass;
    protected $host;
    protected $port;
    protected $path;
    protected $query;
    protected $fragment;

    public function __construct($url)
    {
        $this->original_url = trim($url);
        if ($this->is_protocol_relative()) {
            $url = 'http:' . $url;
        }
        $urlo = parse_url($url);
        if (isset($urlo['scheme']) && !$this->is_protocol_relative()) {
            $this->scheme = $urlo['scheme'];
        }
        if (isset($urlo['user'])) $this->user = $urlo['user'];
        if (isset($urlo['pass'])) $this->pass = $urlo['pass'];
        if (isset($urlo['host'])) $this->host = $urlo['host'];
        if (isset($urlo['port'])) $this->port = $urlo['port'];
        if (isset($urlo['path'])) $this->path = $urlo['path'];
        if (isset($urlo['query'])) $this->query = $urlo['query'];
        if (isset($urlo['fragment'])) $this->fragment = $urlo['fragment'];
    }

    /**
     * Checks whether this URL is a WWW resource (as opposed to e.g. mailto: and tel: urls)
     *
     * @return bool
     */
    public function is_www()
    {
        // since we are dealing with URLs in HTML pages
        // we assume that if no scheme is provided it is http or https
        return ($this->scheme == 'http' || $this->scheme == 'https' || $this->scheme == '');
    }

    /**
     * @return bool
     */
    public function is_local()
    {
        return (substr($this->original_url, 0, 1) == '#');
    }

    public function is_relative()
    {
        return ($this->scheme == '' && $this->host == '' && substr($this->path, 0, 1) != '/');
    }

    public function is_host_relative()
    {
        return ($this->scheme == '' && $this->host == '' && substr($this->path, 0, 1) == '/');
    }

    public function is_absolute()
    {
        return ($this->scheme != '');
    }

    public function is_protocol_relative()
    {
        return (substr($this->original_url, 0, 2) == '//');
    }

    public function __toString() {
        //TODO: don't ignore user, pass and port
        return
            ($this->scheme ? $this->scheme . ':' : '')
            . ($this->host ? '//' . $this->host : '')
            . ($this->path ? $this->path : '')
            . ($this->query ? '?' . $this->query : '')
            . ($this->fragment ? '#' . $this->fragment : '')
        ;
    }

    public function setFragment($fragment)
    {
        $this->fragment = $fragment;
    }

    public function getFragment()
    {
        return $this->fragment;
    }

    public function setHost($host)
    {
        $this->host = $host;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function setPass($pass)
    {
        $this->pass = $pass;
    }

    public function getPass()
    {
        return $this->pass;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setPort($port)
    {
        $this->port = $port;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function setQuery($query)
    {
        $this->query = $query;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function setScheme($scheme)
    {
        $this->scheme = $scheme;
    }

    public function getScheme()
    {
        return $this->scheme;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getFilename()
    {
        return basename($this->path);
    }

    public function hasQueryParameter($name)
    {
        if ($this->query == '') return false;
        $params = array();
        parse_str($this->query, $params);
        return isset($params[$name]);
    }
    public function getQueryParameter($name)
    {
        if ($this->query == '') return null;
        $params = array();
        parse_str($this->query, $params);
        if (isset($params[$name])) return $params[$name];
        else return null;
    }
    public function setQueryParameter($name, $value)
    {
        $params = array();
        if ($this->query != '') parse_str($this->query, $params);
        $params[$name] = $value;
        $this->query = http_build_query($params);
    }

    /**
     * @param Url|string|null $relurl
     */
    public function makeAbsolute($relurl = null) {
        if (is_string($relurl)) $relurl = new static($relurl);
        if ($this->is_www() && $this->is_relative() && $relurl instanceof Url) {
            $this->host = $relurl->getHost();
            $this->scheme = $relurl->getScheme();
            $this->user = $relurl->getUser();
            $this->pass = $relurl->getPass();
            $this->port = $relurl->getPort();
            $this->path = self::buildAbsolutePath($this->path, $relurl->getPath());
        }
    }

    static public function buildAbsolutePath($relative_path, $referring_path) {
        $basedir = dirname($referring_path);
        if ($basedir == '.' || $basedir == '/') $basedir = '';
        return $basedir . '/' . $relative_path;
    }


}
