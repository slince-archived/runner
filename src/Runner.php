<?php
/**
 * slince runner library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Runner;

use GuzzleHttp\Client;

class Runner
{
    /**
     * runner状态，等待
     * @var int
     */
    const STATUS_WAITING = 0;

    /**
     * runner状态，正在执行
     * @var int
     */
    const STATUS_RUNNING = 1;

    /**
     * 执行的测试链，一个runner只能执行一条测试链
     * @var ExaminationChain
     */
    protected $examinationChain;

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * 当前runner状态
     * @var int
     */
    protected $status = self::STATUS_WAITING;

    function __construct(ExaminationChain $examinationChain = null)
    {
        $this->examinationChain = $examinationChain;
    }

    /**
     * 启动runner
     */
    function run()
    {
        //修改当前runner状态
        $this->status = self::STATUS_RUNNING;
        while ($this->examinationChain->valid()) {
            $this->executeExamination($this->examinationChain->dequeue());
        }
        //执行结束，状态置为waiting
        $this->status = self::STATUS_WAITING;
    }

    /**
     * 执行测试
     * @param Examination $examination
     */
    function executeExamination(Examination $examination)
    {
        $response = $this->requestApi($examination->getApi());
        $examination->getReport()->write('response', $response);
        $examination->setIsExecuted(true);
        $this->runAssertions($examination);
    }

    /**
     * @param Api $api
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    protected function requestApi(Api $api)
    {
        $options = [];
        if ($auth = $api->getAuth()) {
            $options['auth'] = $auth;
        }
        return $this->httpClient->request($api->getRequestMethod(), $api->getUrl(), $options);
    }

    protected function runAssertions(Examination $examination)
    {
        
    }
}