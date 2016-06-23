<?php
/**
 * slince template collector library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Runner;

class CookieContainer
{
    /**
     * cookies
     * @var array
     */
    protected $cookies = [];

    function __construct(array $cookies = [])
    {
        $this->cookies = $cookies;
    }

    /**
     * 设置cookie
     * @param $name
     * @param $value
     * @param array $options
     */
    function set($name, $value, array $options = [])
    {
        $options += [
            'name' => $name,
            'value' => $value
        ];
        $cookie = Cookie::createFromArray($options);
        $this->add($cookie);
    }

    /**
     * 删除某个cookie
     * @param $name
     */
    function delete($name)
    {
        foreach ($this->cookies as $key => $cookie) {
            if ($cookie->getName() == $name) {
                unset($this->cookies[$key]);
            }
        }
    }

    /**
     * 批量添加cookies
     * @param array $cookies
     */
    function addCookies(array $cookies = [])
    {
        foreach ($cookies as $cookie) {
            $this->add($cookie);
        }
    }

    /**
     * 添加cookie
     * @param Cookie $cookie
     */
    function add(Cookie $cookie)
    {
        $this->cookies[] = $cookie;
    }

    /**
     * 移除cookie
     * @param Cookie $cookie
     */
    function remove(Cookie $cookie)
    {
        if (($pos = array_search($cookie, $this->cookies)) !== false) {
            unset($this->cookies[$pos]);
        }
    }

    /**
     * 获取符合这个规则
     * @param Url $url
     */
    function getValidCookies(Url $url)
    {
        
    }
}