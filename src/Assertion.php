<?php
/**
 * slince runner library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Runner;

use GuzzleHttp\Psr7\Response;
use Slince\Runner\Assertion\AssertionInterface;

class Assertion
{
    /**
     * 对应的断言对象
     * @var mixed
     */
    protected $assertion;

    /**
     * 断言对象的方法
     * @var string
     */
    protected $method;

    /**
     * 闭包传入的参数
     * @var array
     */
    protected $parameters = [];

    /**
     * 断言执行结果
     * @var bool
     */
    protected $executedResult;

    /**
     * 断言失败给出的提示
     * @var string
     */
    protected $message;

    function __construct(AssertionInterface $assertion, $method, array $parameters = [])
    {
        $this->assertion = $assertion;
        $this->method = $method;
        $this->parameters = $parameters;
    }

    /**
     * 执行断言
     * @param Response $response
     * @return boolean
     */
    function execute(Response $response)
    {
        if ($this->assertion->getResponse() == null) {
            $this->assertion->setResponse($response);
        }
        try {
            $result = call_user_func_array([$this->assertion, $this->method], $this->parameters);
            $message = '';
        } catch (\Exception $e) {
            $result = false;
            $message = $e->getMessage();
        }
        $this->executedResult = $result !== false;
        $this->message = $message;
        return $this->executedResult;
    }

    /**
     * 获取message
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * 获取断言执行结果
     * @return bool
     */
    public function getExecutedResult()
    {
       return $this->executedResult;
    }

    /**
     * 获取断言对应的方法
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * 获取传给断言的参数
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}