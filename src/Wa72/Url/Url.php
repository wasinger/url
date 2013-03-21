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

    protected $query_array = array();

    public function __construct($url)
    {
        $this->original_url = trim($url);
        // Workaround: parse_url doesn't recognize host in protocol relative urls (starting with //)
        // so temporarily prepend "http:" for parsing and remove it later
        if ($this->is_protocol_relative()) {
            $url = 'http:' . $url;
        }
        $urlo = parse_url($url);
        if (isset($urlo['scheme']) && !$this->is_protocol_relative()) {
            $this->scheme = strtolower($urlo['scheme']);
        }
        if (isset($urlo['user'])) $this->user = $urlo['user'];
        if (isset($urlo['pass'])) $this->pass = $urlo['pass'];
        if (isset($urlo['host'])) $this->host = strtolower($urlo['host']);
        if (isset($urlo['port'])) $this->port = intval($urlo['port']);
        if (isset($urlo['path'])) $this->path = $urlo['path'];
        if (isset($urlo['query'])) $this->query = $urlo['query'];
        if ($this->query != '') parse_str($this->query, $this->query_array);
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

    /**
     * @return bool
     */
    public function is_relative()
    {
        return ($this->scheme == '' && $this->host == '' && substr($this->path, 0, 1) != '/');
    }

    /**
     * @return bool
     */
    public function is_host_relative()
    {
        return ($this->scheme == '' && $this->host == '' && substr($this->path, 0, 1) == '/');
    }

    /**
     * @return bool
     */
    public function is_absolute()
    {
        return ($this->scheme != '');
    }

    /**
     * @return bool
     */
    public function is_protocol_relative()
    {
        return (substr($this->original_url, 0, 2) == '//');
    }

    /**
     * @return string
     */
    public function __toString() {
        $url = ($this->scheme ? $this->scheme . ':' : '');
        if ($this->host || $this->scheme == 'file') $url .= '//';
        if ($this->host) {
            if ($this->user) {
                $url .= $this->user . ($this->pass ? ':' . $this->pass : '') . '@';
            }
            $url .= $this->host;
            if ($this->port) {
                $url .= ':' . $this->port;
            }
        }
        $url .= ($this->path ? $this->path : '');
        $url .= ($this->query ? '?' . $this->query : '');
        $url .= ($this->fragment ? '#' . $this->fragment : '');
        return $url;
    }

    /**
     * @param string $fragment
     * @return Url $this
     */
    public function setFragment($fragment)
    {
        $this->fragment = $fragment;
        return $this;
    }

    /**
     * @return string
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * @param string $host
     * @return Url $this
     */
    public function setHost($host)
    {
        $this->host = strtolower($host);
        return $this;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param $pass
     * @return Url $this
     */
    public function setPass($pass)
    {
        $this->pass = $pass;
        return $this;
    }

    /**
     * @return string
     */
    public function getPass()
    {
        return $this->pass;
    }

    /**
     * @param string $path
     * @return Url $this
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param int $port
     */
    public function setPort($port)
    {
        $this->port = intval($port);
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Set the query from an already url encoded query string
     *
     * @param string $query The query string, must be already url encoded!!
     * @return Url $this
     */
    public function setQuery($query)
    {
        $this->query = $query;
        parse_str($this->query, $this->query_array);
        return $this;
    }

    /**
     * @return string The url encoded query string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param string $scheme
     * @return Url $this
     */
    public function setScheme($scheme)
    {
        $this->scheme = strtolower($scheme);
        return $this;
    }

    /**
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * @param string $user
     * @return Url $this
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Get the filename from the path (the last path segment as returned by basename())
     *
     * @return string
     */
    public function getFilename()
    {
        return static::filename($this->path);
    }

    /**
     * Get the directory name from the path
     *
     * @return string
     */
    public function getDirname()
    {
        return static::dirname($this->path);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasQueryParameter($name)
    {
        return isset($this->query_array[$name]);
    }

    /**
     * @param string $name
     * @return string|null
     */
    public function getQueryParameter($name)
    {
        if (isset($this->query_array[$name])) return $this->query_array[$name];
        else return null;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return Url $this
     */
    public function setQueryParameter($name, $value)
    {
        $this->query_array[$name] = $value;
        $this->query = http_build_query($this->query_array);
        return $this;
    }

    /**
     * @param array $query_array
     * @return Url $this
     */
    public function setQueryFromArray(array $query_array)
    {
        $this->query_array = $query_array;
        $this->query = http_build_query($this->query_array);
        return $this;
    }

    /**
     * Get the query parameters as array
     *
     * @return array
     */
    public function getQueryArray()
    {
        return $this->query_array;
    }

    /**
     * Make this (relative) URL absolute using another absolute base URL
     *
     * Does nothing if this URL is not relative
     *
     * @param Url|string|null $baseurl
     * @return Url $this
     */
    public function makeAbsolute($baseurl = null) {
        if (is_string($baseurl)) $baseurl = new static($baseurl);
        if ($this->is_www() && $this->is_relative() && $baseurl instanceof Url) {
            $this->host = $baseurl->getHost();
            $this->scheme = $baseurl->getScheme();
            $this->user = $baseurl->getUser();
            $this->pass = $baseurl->getPass();
            $this->port = $baseurl->getPort();
            $this->path = static::buildAbsolutePath($this->path, $baseurl->getPath());
        }
        return $this;
    }

    /**
     * @param Url|string $another_url
     * @return bool
     */
    public function equals($another_url)
    {
        if (!($another_url instanceof Url)) $another_url = new static($another_url);
        return $this->getScheme() == $another_url->getScheme()
            && $this->getUser() == $another_url->getUser()
            && $this->getPass() == $another_url->getPass()
            && $this->equalsHost($another_url->getHost())
            && $this->getPort() == $another_url->getPort()
            && $this->equalsPath($another_url->getPath())
            && $this->equalsQuery($another_url->getQuery())
            && $this->getFragment() == $another_url->getFragment()
        ;
    }

    /**
     * @param string $another_path
     * @return bool
     */
    public function equalsPath($another_path)
    {
        // TODO: normalize paths
        return $this->getPath() == $another_path;
    }

    /**
     * @param string|array|Url $another_query
     * @return bool
     */
    public function equalsQuery($another_query)
    {
        $another_query_array = array();
        if (is_array($another_query)) $another_query_array = $another_query;
        elseif ($another_query instanceof Url) $another_query_array = $another_query->getQueryArray();
        else parse_str((string) $another_query, $another_query_array);

        return !count(array_diff_assoc($this->getQueryArray(), $another_query_array));
    }

    /**
     * @param string $another_hostname
     * @return bool
     */
    public function equalsHost($another_hostname)
    {
        // TODO: normalize IDN
        return $this->getHost() == strtolower($another_hostname);
    }

    /**
     * Build an absolute path from given relative path and base path
     *
     * @param string $relative_path
     * @param string $basepath
     * @return string
     */
    static public function buildAbsolutePath($relative_path, $basepath) {
        $basedir = static::dirname($basepath);
        if ($basedir == '.' || $basedir == '/' || $basedir == '\\') $basedir = '';
        return static::normalizePath($basedir . '/' . $relative_path);
    }

    /**
     * @param string $path
     * @return string
     */
    static public function normalizePath($path)
    {
        $path = preg_replace('|/\./|', '/', $path);   // entferne /./
        $path = preg_replace('|^\./|', '', $path);    // entferne ./ am Anfang
        $i = 0;
        while (preg_match('|[^/]+/\.{2}/|', $path) && $i < 10) {
            $path = preg_replace('|([^/]+)(/\.{2}/)|e', "'\\1'=='..'?'\\0':''", $path);
            $i++;
        }
        return $path;
    }

    /**
     * @param $path
     * @return string
     */
    static public function filename($path)
    {
        if (substr($path, -1) == '/') return '';
        else return basename($path);
    }

    static public function dirname($path)
    {
        if (substr($path, -1) == '/') return substr($path, 0, -1);
        else return dirname($path);
    }

    /**
     * @param string $url
     * @return Url
     */
    static public function parse($url)
    {
        return new static($url);
    }
}
