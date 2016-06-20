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
     * @param array $query
     * @param array $formParams
     * @param array $auth
     * @param int $timeout
     * @param bool $followRedirect
     * @param array $headers
     * @param array $cookies
     * @param bool $enableCookie
     * @param mixed $proxy
     * @param string $cert
     * @return Api
     */
    static function createApi(
        $url,
        $method = 'GET',
        array $query = [],
        array $posts = [],
        array $files = [],
        array $auth = [],
        $timeout = 0,
        $followRedirect = false,
        array $headers = [],
        array $cookies = [],
        $enableCookie = false,
        $proxy = null,
        $cert = null)
    {
        return new Api($url, $method, $query, $posts, $files, $auth, $timeout, $followRedirect,
            $headers, $cookies, $enableCookie, $proxy, $cert);
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