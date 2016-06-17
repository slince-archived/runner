<?php
/**
 * slince runner library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Runner;

use Slince\Runner\Url;

class Api
{
    /**
     * @var Url
     */
    protected $url;

    protected $allowMethods = [];

    protected $headers = [];

    protected $cookies;

    protected $auth;

    function getRequestMethod()
    {
        return reset($this->allowMethods);
    }

    /**
     * @return Url
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return mixed
     */
    public function getAuth()
    {
        return $this->auth;
    }
}