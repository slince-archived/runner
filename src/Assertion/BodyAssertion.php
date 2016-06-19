<?php
/**
 * slince runner library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Runner\Assertion;

use Cake\Utility\Hash;
use GuzzleHttp\Psr7\Response;
use Slince\Runner\Exception\InvalidArgumentException;

class BodyAssertion extends AbstractAssertion
{

    protected $body;

    /**
     * json格式
     * @var object
     */
    protected $json;

    /**
     * 设置response
     * @param Response $response
     */
    function setResponse(Response $response)
    {
        parent::setResponse($response);
        $this->body = $response->getBody();
    }

    /**
     * 获取body
     * @return mixed
     */
    function getBody()
    {
        return $this->body;
    }

    /**
     * 格式化为json返回
     * @return mixed
     */
    protected function json()
    {
        if (empty($this->json)) {
            $this->json = json_decode($this->body, true);
            if (json_last_error() != JSON_ERROR_NONE) {
                throw new InvalidArgumentException(sprintf("Invalid Json Format"));
            }
        }
        return $this->json;
    }

    /**
     * 获取指定参数
     * @param $name
     * @return mixed
     */
    protected function getParameter($name)
    {
        return Hash::get($this->json(), $name);
    }
    /**
     * 是否是json格式
     * @param $result
     * @return bool
     */
    function isJson($result)
    {
        json_decode($this->body);
        return $result == (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * 是否是xml
     * @param $result
     * @return bool
     */
    function isXml($result)
    {
        $parser = xml_parser_create();
        xml_parse($parser, $this->body);
        return $result == (xml_get_error_code($parser) == XML_ERROR_NONE);
    }
    
    /**
     * 判断存在指定参数
     * @param $name
     * @return bool
     */
    function hasParameter($name)
    {
        return Hash::get($this->json(), $name, false) != false;
    }

    /**
     * 批量判断存在指定参数
     * @param $names
     * @return bool
     */
    function hasParameters($names)
    {
        foreach ($names as $name) {
            if (!$this->hasParameter($name)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 指定参数等于
     * @param $name
     * @param $value
     * @return bool
     */
    function parameterEqual($name, $value)
    {
        $parameter = $this->getParameter($name);
        if (!is_null($parameter)) {
            return $parameter == $value;
        }
        return false;
    }

    /**
     * 参数符合正则约束
     * @param $name
     * @param $pattern
     * @return mixed
     */
    function parameterRegex($name, $pattern)
    {
        $parameter = $this->getParameter($name);
        return preg_match("#{$pattern}#", Hash::get($this->json(), $parameter));
    }
}