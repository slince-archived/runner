<?php
/**
 * slince runner library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Runner\Assertion;

use GuzzleHttp\Psr7\Response;
use Slince\Runner\Exception\InvalidArgumentException;

class HeaderAssertion extends AbstractAssertion
{
    protected $headers;

    function execute(Response $response)
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
     * 是否存在header
     * @param $header
     * @return bool
     */
    function hasHeader($header)
    {
        return $this->response->hasHeader($header);
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
}