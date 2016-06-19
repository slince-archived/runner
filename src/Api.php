<?php
/**
 * slince runner library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Runner;

class Api
{
    /**
     * @var Url
     */
    protected $url;

    /**
     * 请求方法
     * @var string
     */
    protected $method = 'GET';

    /**
     * query参数
     * @var array
     */
    protected $query = [];

    /**
     * auth验证
     * @var array
     */
    protected $auth = [];

    /**
     * 超时时间
     * @var float
     */
    protected $timeout = 0;

    /**
     * 是否跟随转向
     * @var bool
     */
    protected $followRedirect = false;
    /**
     * 自定义header
     * @var array
     */
    protected $headers = [];

    /**
     * 自定义cookie
     * @var array
     */
    protected $cookies = [];

    /**
     * 是否开启cookie
     * @var bool
     */
    protected $enableCookie = false;

    /**
     * 代理
     * @var mixed
     */
    protected $proxy = null;
    /**
     * 自定义证书
     * @var string
     */
    protected $cert = null;

    function __construct(
        $url,
        $method = 'GET',
        array $query = [],
        array $auth = [],
        $timeout = 0,
        $followRedirect = false,
        array $headers = [],
        array $cookies = [],
        $enableCookie = false,
        $proxy = null,
        $cert = null
    ) {
        $this->url = Url::createFromUrl($url);
        $this->method = $method;
        $this->query = $query;
        $this->auth = $auth;
        $this->timeout = $timeout;
        $this->followRedirect = $followRedirect;
        $this->headers = $headers;
        $this->cookies = $cookies;
        $this->enableCookie = $enableCookie;
        $this->proxy = $proxy;
        $this->cert = $cert;
    }

    public function setUrl(Url $url)
    {
        $this->url = $url;
    }

    /**
     * @return Url
     */
    public function getUrl()
    {
        return $this->url;
    }

    public function setMethod($method)
    {
        $this->method = $method;
    }

    function getMethod()
    {
        return $this->method;
    }

    public function setQuery($query)
    {
        $this->query = $query;
    }

    public function getQuery()
    {
        return $this->query;
    }
    
    public function setAuth($auth)
    {
        $this->auth = $auth;
    }

    /**
     * @return mixed
     */
    public function getAuth()
    {
        return $this->auth;
    }

    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function setCookies($cookies)
    {
        $this->cookies = $cookies;
    }

    public function getCookies()
    {
        return $this->cookies;
    }

    public function setEnableCookie($enableCookie)
    {
        $this->enableCookie = $enableCookie;
    }

    function getEnableCookie()
    {
        return $this->enableCookie;
    }

    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    public function getTimeout()
    {
        return $this->timeout;
    }

    public function setProxy($proxy)
    {
        $this->proxy = $proxy;
    }

    public function getProxy()
    {
        return $this->proxy;
    }

    public function setCert($cert)
    {
        $this->cert = $cert;
    }

    public function getCert()
    {
        return $this->cert;
    }

    public function setFollowRedirect($followRedirect)
    {
        $this->followRedirect = $followRedirect;
    }

    public function getFollowRedirect()
    {
        return $this->followRedirect;
    }
}