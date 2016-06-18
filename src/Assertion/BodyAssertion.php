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
     * 执行测试
     * @param Response $response
     */
    function execute(Response $response)
    {
        parent::execute($response);
        $this->body = $response->getBody();
    }

    function getBody()
    {
        return $this->body;
    }

    /**
     * body是否是合法的json格式
     * @return bool
     */
    function isJson()
    {
        json_decode($this->body);
        return json_last_error() == JSON_ERROR_NONE;
    }

    /**
     * 是否是xml
     * @return bool
     */
    function isXml()
    {
        $parser = xml_parser_create();
        xml_parse($parser, $this->body);
        return xml_get_error_code() == XML_ERROR_NONE;
    }

    /**
     * 格式化为json返回
     * @return mixed
     */
    function json()
    {
        if (empty($this->json)) {
            $this->json = json_decode($this->body, true);
            if (json_last_error == JSON_ERROR_NONE) {
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
    function getParameter($name)
    {
        return Hash::get($this->json(), $name);
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