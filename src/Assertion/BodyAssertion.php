<?php
/**
 * slince runner library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Runner\Assertion;

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
            $this->json = json_decode($this->body);
            if (json_last_error == JSON_ERROR_NONE) {
                throw new InvalidArgumentException(sprintf("Invalid Json Format"));
            }
        }
        return $this->json;
    }
}