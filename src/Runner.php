<?php
/**
 * slince runner library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Runner;

use Cake\Utility\Hash;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Psr7\Response;
use Slince\Cache\ArrayCache;
use Slince\Event\Dispatcher;
use Slince\Event\Event;
use Slince\Runner\Exception\InvalidArgumentException;
use Slince\Runner\Exception\RuntimeException;
use Symfony\Component\Filesystem\Filesystem;

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
     * runner启动事件
     * @var string
     */
    const EVENT_RUN = 'run';

    /**
     * runner结束事件
     * @var string
     */
    const EVENT_FINISH = 'finish';

    /**
     * 测试项开始执行测试事件
     * @var string
     */
    const EVENT_EXAMINATION_EXECUTE = 'examination.execute';

    /**
     * 测试项开始执行测试结束事件
     * @var string
     */
    const EVENT_EXAMINATION_EXECUTED = 'examination.executed';

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

    /**
     * @var ArrayCache
     */
    protected $arguments;

    /**
     * dispatcher
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    function __construct(ExaminationChain $examinationChain = null)
    {
        $this->examinationChain = $examinationChain;
        $this->arguments = new ArrayCache();
        $this->httpClient = new Client();
        $this->dispatcher = new Dispatcher();
        $this->filesystem = new Filesystem();
    }

    /**
     * 启动runner
     */
    function run()
    {
        $this->dispatcher->dispatch(self::EVENT_RUN, new Event(self::EVENT_RUN, $this));
        //修改当前runner状态
        $this->status = self::STATUS_RUNNING;
        foreach ($this->examinationChain as $examination) {
            $this->executeExamination($examination);
        }
        //执行结束，状态置为waiting
        $this->status = self::STATUS_WAITING;
        $this->dispatcher->dispatch(self::EVENT_FINISH, new Event(self::EVENT_FINISH, $this));
    }

    /**
     * 获取当前runner工作状态
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * 获取测试链
     * @return ExaminationChain
     */
    public function getExaminationChain()
    {
        return $this->examinationChain;
    }

    /**
     * 获取http请求客户端
     * @return Client
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * 获取事件调度器
     * @return Dispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * 获取截取的参数
     * @return ArrayCache
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * 执行测试
     * @param Examination $examination
     * @return bool
     */
    function executeExamination(Examination $examination)
    {
        $this->dispatcher->dispatch(self::EVENT_EXAMINATION_EXECUTE, new Event(
            self::EVENT_EXAMINATION_EXECUTE, $this, [
                'examination' => $examination
            ])
        );
        try {
            $response = $this->requestApi($examination->getApi());
        } catch (\Exception $e) {
            //如果接口请求的过程中出现异常，则终止测试过程
            $examination->executed(Examination::STATUS_INTERRUPT);
            $examination->getReport()->write('exception', $e);
            return false;
        }
        $examination->getReport()->write('response', $response);
        $this->extractArguments($examination, $response);
        $this->runAssertions($examination, $response);
        $this->dispatcher->dispatch(self::EVENT_EXAMINATION_EXECUTED, new Event(
                self::EVENT_EXAMINATION_EXECUTED, $this, [
                'examination' => $examination
            ])
        );
    }

    /**
     * @param Api $api
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    protected function requestApi(Api $api)
    {
        //支持的option
        $options = [
            'timeout' => $api->getTimeout(),
            'headers' => $api->getHeaders(),
            'proxy' => $api->getProxy(),
            'allow_redirects' => $api->getFollowRedirect()
        ];
        if ($auth = $api->getAuth()) {
            $options['auth'] = $auth;
        }
        //需要兼容url中带有query参数的情况
        if ($query = $api->getQuery()) {
            parse_str($api->getUrl()->getQuery(), $urlQuery);
            $options['query'] = array_merge($urlQuery, $query);
        }
        //post参数
        if ($posts = $api->getPosts()) {
            $options['form_params'] = $posts;
        }
        //文件上传
        if ($files = $api->getFiles()) {
            $multipartParams = [];
            if (!empty($options['form_params'])) {
                $multipartParams = $this->convertFormParamsToMultipart($options['form_params']);
            }
            $multipartParams = array_merge($multipartParams, $this->convertFilesToMultipart($files));
            $options['multipart'] = $multipartParams;
            unset($options['form_params']);
        }
        //如果证书文件路径不是绝对路径则从工作目录下查找
        if ($cert = $api->getCert()) {
            if (!$this->filesystem->isAbsolutePath($cert)) {
                $cert =  realpath(getcwd() .DIRECTORY_SEPARATOR . $cert);
            }
            $options['cert'] = $cert;
        }
        //cookies
        if ($cookies = $api->getCookies()) {
            $options['cookies'] = CookieJar::fromArray($api->getCookies(), $api->getUrl()->getHost());
        }
        //预先替换掉参数里的所有变量，注意如果有变量被声明单没有替换的话会终止
        $method = $this->processValue($api->getMethod());
        $url = $this->processValue(strval($api->getUrl()));
        $options = $this->processOptions($options);
        return $this->httpClient->request($method, $url, $options);
    }

    /**
     * 转换form params成multipart格式
     * @param $formParams
     * @return array
     */
    protected function convertFormParamsToMultipart($formParams)
    {
        $posts = [];
        foreach ($formParams as $name => $value) {
            $posts[] = [
                'name' => $name,
                'contents' => $value
            ];
        }
        return $posts;
    }


    /**
     * 转换files到multipart格式
     * @param $files
     * @return array
     */
    protected function convertFilesToMultipart($files)
    {
        $posts = [];
        foreach ($files as $name => $file) {
            if (!$this->filesystem->isAbsolutePath($file)) {
                $file = getcwd() . DIRECTORY_SEPARATOR . $file;
            }
            if (!file_exists($file)) {
                throw new InvalidArgumentException();
            }
            $posts[] = [
                'name' => $name,
                'contents' => fopen($file, 'r')
            ];
        }
        return $posts;
    }
    /**
     * 执行测试任务所有的断言
     * @param Examination $examination
     * @param Response $response
     */
    protected function runAssertions(Examination $examination, Response $response)
    {
        $executedResult = true;
        foreach ($examination->getAssertions() as $assertion) {
            if (!$assertion->execute($response)) {
                $executedResult = false;
            }
        }
        $examination->executed($executedResult ? Examination::STATUS_SUCCESS : Examination::STATUS_FAILED);
    }

    /**
     * 从响应中提取需要catch的参数
     * @param Examination $examination
     * @param Response $response
     * @return bool
     * @throws InvalidArgumentException
     */
    protected function extractArguments(Examination $examination, Response $response)
    {
        $catch = $examination->getCatch();
        if (empty($catch)) {
            return true;
        }
        //从header里面提取
        if (isset($catch['header']) && is_array($catch['header'])) {
            foreach ($catch['header'] as $parameter => $name) {
                if (is_numeric($parameter)) {
                    $newArgumentName = $name;
                    $oldArgumentName = $name;
                } else {
                    $newArgumentName = $name;
                    $oldArgumentName = $parameter;
                }
                $this->arguments->set($newArgumentName, $response->getHeaderLine($oldArgumentName));
            }
        }
        //从body里面提取
        if (isset($catch['body']) && is_array($catch['body'])) {
            $json = json_decode($response->getBody(), true);
            if (json_last_error() != JSON_ERROR_NONE) {
                throw new InvalidArgumentException(sprintf("Invalid Json Format"));
            }
            foreach ($catch['body'] as $parameter => $name) {
                if (is_numeric($parameter)) {
                    $newArgumentName = $name;
                    $oldArgumentName = $name;
                } else {
                    $newArgumentName = $name;
                    $oldArgumentName = $parameter;
                }
                $this->arguments->set($newArgumentName, Hash::get($json, $oldArgumentName));
            }
        }
        return true;
    }
        /**
     * 处理options
     * @param $options
     * @return array
     */
    protected function processOptions(array $options)
    {
        $processedOptions = [];
        foreach ($options as $key => $option) {
            $processedOptions[$key] = is_array($option) ?
                $this->processOptions($option)
                : $this->processValue($option);
        }
        return $processedOptions;
    }
    /**
     * 替换参数里的变量
     * @param $value
     * @return mixed
     */
    protected function processValue($value)
    {
        if (is_scalar($value)) {
            return  preg_replace_callback('#\{([a-zA-Z0-9_,]*)\}#i', function ($matches) {
                if (!isset($this->arguments[$matches[1]])) {
                    throw new RuntimeException("The variable [$matches[1]] does not exists");
                }
                return $this->arguments[$matches[1]];
            }, $value);
        } elseif (is_array($value)) {
            return call_user_func_array([$this, 'processValue'], $value);
        } else {
            return $value;
        }
    }
}