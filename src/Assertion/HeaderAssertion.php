<?php
/**
 * slince runner library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Runner\Assertion;

use GuzzleHttp\Psr7\Response;

class HeaderAssertion extends AbstractAssertion
{
    protected $headers;

    /**
     * 设置response
     * @param Response $response
     */
    function setResponse(Response $response)
    {
        parent::execute($response);
        $this->headers = $response->getHeaders();
    }

    /**
     * 获取状态码
     * @return mixed
     */
    function getStatusCode()
    {
        return $this->getStatusCode();
    }

    /**
     * 获取header line
     * @param $header
     * @return string
     */
    function getHeaderLine($header)
    {
        return $this->response->getHeaderLine($header);
    }

    /**
     * 获取header
     * @param $header
     * @return array
     */
    function getHeader($header)
    {
        return $this->response->getHeader($header);
    }

    /**
     * 是否存在header
     * @param $header
     * @return bool
     */
    function hasHeader($header)
    {
        return $this->response->hasHeader($header);
    }

    /**
     * 判断指定header的值
     * @param $header
     * @param $value
     * @return bool
     */
    function headerEqual($header, $value)
    {
        return $this->getHeaderLine($header) == $value;
    }

    /**
     * header符合某个正则规则
     * @param $header
     * @param $pattern
     * @return mixed
     */
    function headerRegex($header, $pattern)
    {
        return preg_match("#{$pattern}#", $this->getHeaderLine($header));
    }

    /**
     * 判断某个header是否存在指定的值
     * @param $header
     * @param array $values
     * @return bool
     */
    function headerHasValues($header, array $values)
    {
        $headerValues = $this->getHeader($header);
        return empty(array_diff($values, $headerValues));
    }
}