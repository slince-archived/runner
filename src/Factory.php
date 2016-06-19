<?php
/**
 * slince runner library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Runner;

class Factory
{
    /**
     * 创建api
     * @param $url
     * @param string $method
     * @param array $auth
     * @param int $timeout
     * @param bool $followRedirect
     * @param array $headers
     * @param array $cookies
     * @param bool $enableCookie
     * @param string $cert
     * @return Api
     */
    static function createApi(
        $url,
        $method = 'GET',
        array $auth = [],
        $timeout = 0,
        $followRedirect = false,
        array $headers = [],
        array $cookies = [],
        $enableCookie = false,
        $cert = '')
    {
        return new Api($url, $method, $auth, $timeout, $followRedirect, $headers, $cookies, $enableCookie, $cert);
    }

    /**
     * 创建测试项
     * @param Api $api
     * @param array $assertions
     * @return Examination
     */
    static function createExamination(Api $api, array $assertions = [], $id = null)
    {
        return new Examination($api, $assertions, $id);
    }

    /**
     * 创建runner
     * @param ExaminationChain|null $examinationChain
     * @return Runner
     */
    static function createRunner(ExaminationChain $examinationChain = null)
    {
        return new Runner($examinationChain);
    }

    /**
     * 创建一个测试流水
     * @return Runner
     */
    static function create()
    {
        $examinationChain = new ExaminationChain();
        return static::createRunner($examinationChain);
    }
}