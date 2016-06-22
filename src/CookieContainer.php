<?php
/**
 * slince template collector library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Runner;


class CookieContainer
{
    protected $cookies = [];

    function __construct(array $cookies = [])
    {
        $this->cookies = $cookies;
    }

    function add(Cookie $cookie)
    {
        $this->cookies[] = $cookie;
    }

    function remove(Cookie $cookie)
    {
        if (($pos = array_search($cookie, $this->cookies)) !== false) {
            unset($this->cookies[$pos]);
        }
    }
}