<?php
/**
 * slince runner library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Runner;

use Slince\Runner\Assertion\ResponseAssertion;
use Slince\Runner\Assertion\HeaderAssertion;
use Slince\Runner\Assertion\BodyAssertion;

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

    /**
     * 根据配置创建runner
     * @param array $config
     * @return Runner
     */
    static function createFromConfig($config)
    {
        $runner = static::create();
        //初始化参数
        if (isset($config['arguments'])) {
            foreach ($config['arguments'] as $name => $value) {
                $runner->getArguments()->add($name, $value);
            }
        }
        //默认配置
        $defaults = isset($config['defaults']) ? $config['defaults'] : [];
        $chain = $runner->getExaminationChain();
        foreach ($config['requests'] as $request) {
            $request = array_merge_recursive($defaults, $request);
            $api = static::createApi($request['url'], $request['method'],
                isset($request['options']['query']) ? $request['options']['query'] : [],
                isset($request['options']['posts']) ? $request['options']['posts'] : [],
                isset($request['options']['files']) ? $request['options']['files'] : [],
                isset($request['options']['auth']) ? $request['options']['auth'] : [],
                isset($request['options']['timeout']) ? $request['options']['timeout'] : 0,
                isset($request['options']['followRedirect']) ? $request['options']['followRedirect'] : false,
                isset($request['options']['headers']) ? $request['options']['headers'] : [],
                isset($request['options']['cookies']) ? $request['options']['cookies'] : [],
                isset($request['options']['enableCookie']) ? $request['options']['enableCookie'] : false,
                isset($request['options']['proxy']) ? $request['options']['proxy'] : null,
                isset($request['options']['cert']) ? $request['options']['cert'] : null
            );
            $assertions = [];
            foreach ($request['assertions'] as $type => $assertionConfigs) {
                $assertions = array_merge($assertions, static::createAssertions($type, $assertionConfigs));
            }
            $chain->enqueue(static::createExamination($api, $assertions, isset($request['id']) ? $request['id'] : null));
        }
        return $runner;
    }

    /**
     * 根绝配置创建assertion对象
     * @param $type
     * @param array $assertionConfigs
     * @return array
     */
    protected static function createAssertions($type, array $assertionConfigs)
    {
        //找到assertion class
        switch ($type) {
            case 'response':
                $assertionClass = '\Slince\Runner\Assertion\ResponseAssertion';
                break;
            case 'header':
                $assertionClass = '\Slince\Runner\Assertion\HeaderAssertion';
                break;
            case 'body':
                $assertionClass = '\Slince\Runner\Assertion\BodyAssertion';
                break;
        }
        $assertions = [];
        foreach ($assertionConfigs as $assertionMethod => $arguments) {
            if (is_array($arguments)) {
                if (!is_numeric(key($arguments))) {
                    foreach ($arguments as $name => $argument) {
                        $assertions[] = new Assertion(new $assertionClass(), $assertionMethod, [$name, $argument]);
                    }
                    continue;
                }
            }
            $assertions[] = new Assertion(new $assertionClass(), $assertionMethod, [$arguments]);
        }
        return $assertions;
    }
}