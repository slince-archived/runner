<?php
/**
 * slince runner library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Runner;

use Cake\Utility\Text;

class Examination
{
    /**
     * 测试结果，成功
     * @var string
     */
    const STATUS_SUCCESS = 'success';

    /**
     * 测试结果，失败
     * @var string
     */
    const STATUS_FAILED = 'failed';

    /**
     * 测试结果，中断
     * @var string
     */
    const STATUS_INTERRUPT = 'interrupt';

    /**
     * 测试结果，等待执行
     * @var string
     */
    const STATUS_WAITING = 'waiting';

    /**
     * 测试id
     * @var string
     */
    protected $id;

    /**
     * @var Api
     */
    protected $api;

    /**
     * @var array
     */
    protected $assertions = [];

    /**
     * 响应中需要捕获的参数
     * @var array
     */
    protected $catch = [];
    /**
     * @var Report
     */
    protected $report;

    /**
     * 当前测试状态
     * @var string
     */
    protected $status = self::STATUS_WAITING;

    function __construct(Api $api, array $assertions = [], $id = null)
    {
        $this->api = $api;
        $this->assertions = $assertions;
        $this->report = new Report();
        if (is_null($id)) {
            $id = Text::uuid();
        }
        $this->id = $id;
    }

    /**
     * @return Api
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * @return Report
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * 获取所有的断言
     * @return array
     */
    public function getAssertions()
    {
        return $this->assertions;
    }

    /**
     * 设置断言
     * @param array $assertions
     */
    public function setAssertions(array $assertions)
    {
        $this->assertions = $assertions;
    }

    /**
     * 添加一个断言
     * @param Assertion $assertion
     */
    function addAssertion(Assertion $assertion)
    {
        $this->assertions[] = $assertion;
    }
    /**
     * 测试执行完毕
     * @param $status
     */
    function executed($status)
    {
        $this->status = $status;
    }

    /**
     * 获取测试任务状态
     * @return string
     */
    function getStatus()
    {
        return $this->status;
    }

    /**
     * 当前测试任务是否执行完毕
     * @return bool
     */
    function getIsExecuted()
    {
        return $this->status != self::STATUS_WAITING;
    }

    /**
     * 设置逮捕获参数
     * @param $catch
     */
    public function setCatch($catch)
    {
        $this->catch = $catch;
    }

    /**
     * 获取捕获参数
     * @return array
     */
    public function getCatch()
    {
        return $this->catch;
    }

    public function getId()
    {
        return $this->id;
    }
}