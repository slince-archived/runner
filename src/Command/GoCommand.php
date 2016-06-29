<?php
/**
 * slince template collector library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Runner\Command;

use Slince\Config\Config;
use Slince\Event\Dispatcher;
use Slince\Event\Event;
use Slince\Runner\Assertion;
use Slince\Runner\Examination;
use Slince\Runner\ExaminationChain;
use Slince\Runner\Exception\RuntimeException;
use Slince\Runner\Factory;
use Slince\Runner\Runner;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use PHPExcel;

class GoCommand extends Command
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * 作者
     * @var string
     */
    protected $author = '';

    function configure()
    {
        parent::configure();
        $this->setName('go');
    }
    
    function execute(InputInterface $input, OutputInterface $output)
    {
        $configFile = $input->getOption(self::CONFIG_OPTION);
        try {
            $this->validateConfigFile($configFile);
        } catch (RuntimeException $exception) {
            throw $exception;
        }
        $this->config = $this->readConfigFile($configFile);
        $this->author = $this->config->get('author');
        $runner = $this->buildRunnerTask();
        $this->bindEventsForUi($runner, $output);
        $runner->run();
    }

    /**
     * 绑定事件
     * @param Runner $runner
     * @param OutputInterface $output
     */
    protected function bindEventsForUi(Runner $runner, OutputInterface $output)
    {
        $chain = $runner->getExaminationChain();
        $progressBar = new ProgressBar($output, count($chain));
        $dispatcher = $runner->getDispatcher();
        $dispatcher->bind(Runner::EVENT_RUN, function() use ($output, $progressBar){
            $output->writeln("Hi {$this->author}!");
            $output->writeln("Runner started and will be performed {$progressBar->getMaxSteps()} tasks");
            $output->write(PHP_EOL);
            $progressBar->start();
        });
        //执行新的测试任务
        $dispatcher->bind(Runner::EVENT_EXAMINATION_EXECUTE, function(Event $event) use($output, $progressBar){
            $examination = $event->getArgument('examination');
            $examination->getReport()->write('_beginTime', microtime(true));
        });
        //测试任务执行完毕
        $dispatcher->bind(Runner::EVENT_EXAMINATION_EXECUTE, function(Event $event) use($output, $progressBar){
            $examination = $event->getArgument('examination');
            $examination->getReport()->write('_endTime', microtime(true));
            $consume = microtime(true) - $examination->getReport()->read('_beginTime');
            $examination->getReport()->write('consume', $consume);
            $progressBar->advance(1);
        });
        $dispatcher->bind(Runner::EVENT_FINISH, function() use ($output, $progressBar){
            $progressBar->finish();
            $output->writeln(PHP_EOL);
            $output->writeln("Runner stop,Generating test report");
        });
        $dispatcher->bind(Runner::EVENT_FINISH, function() use ($runner){
            $this->makeReport($runner);
        });
    }

    /**
     * 生成测试报告
     * @param Runner $runner
     */
    protected function makeReport(Runner $runner)
    {
        $excel = new PHPExcel();
        $sheet = $excel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'ID')
            ->setCellValue('B1', 'Url地址')
            ->setCellValue('C1', '请求方法')
            ->setCellValue('D1', '耗时')
            ->setCellValue('E1', '测试结果')
            ->setCellValue('F1', '备注')
            ->setCellValue('G1', '断言结果')
            ->setCellValue('H1', '响应');
        foreach ($this->extractDataFromChain($runner->getExaminationChain()) as $key => $data) {
            $key += 2;
            $sheet->setCellValue("A{$key}", $data['id'])
                ->setCellValue("B{$key}", $data['url'])
                ->setCellValue("C{$key}", $data['method'])
                ->setCellValue("D{$key}", $data['consume'])
                ->setCellValue("E{$key}", $data['status'])
                ->setCellValue("F{$key}", $data['remark'])
                ->setCellValue("G{$key}", $data['assertion'])
                ->setCellValue("H{$key}", $data['response']);
        }

        $writer = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $filename = getcwd() . DIRECTORY_SEPARATOR . 'report.xlsx';
        if (file_exists($filename)) {
            $filename = str_replace('.xlsx', time() . '.xlsx', $filename);
        }
        $writer->save($filename);
    }

    /**
     * 提取报告数据
     * @param ExaminationChain $examinationChain
     * @return array
     */
    protected function extractDataFromChain(ExaminationChain $examinationChain)
    {
        $datas = [];
        foreach ($examinationChain as $examination) {
            $response = $examination->getReport()->read('response');
            $data = [
                'id' => $examination->getId(),
                'url' => $examination->getApi()->getUrl(),
                'method' => $examination->getApi()->getMethod(),
                'consume' => $examination->getReport()->read('consume'),
                'status' => $this->getStatusText($examination->getStatus()),
                'remark' => '',
                'assertion' => $this->reduceAssertionsResults($examination->getAssertions()),
                'response' => $response ? $response->getBody() : ''
            ];
            if ($examination->getStatus() == Examination::STATUS_INTERRUPT) {
                $data['remark'] = $examination->getReport()->read('exception')->getMessage();
            } elseif ($examination->getStatus() == Examination::STATUS_FAILED) {
                $data['remark'] = $this->reduceAssertionsMessage($examination->getAssertions());
            } else {
                $data['remark'] = null;
            }
            $datas[] = $data;
        }
        return $datas;
    }

    /**
     * 将所有断言中的message迭代出来
     * @param array $assertions
     * @return string
     */
    protected function reduceAssertionsMessage(array $assertions)
    {
        $messages = [];
        foreach ($assertions as $assertion) {
            $messages[] = $assertion->getMessage();
        }
        return implode(';', array_filter($messages));
    }
    /**
     * 将断言结果迭代成可存储的字符串
     * @param array $assertions
     * @return string
     */
    protected function reduceAssertionsResults(array $assertions)
    {
        $results = [];
        foreach ($assertions as $assertion) {
            $result = implode(':', [
                $assertion->getMethod(),
                print_r($assertion->getParameters(), true),
                $assertion->getExecutedResult() ? 'true' : 'false'
            ]);
            $results[] = $result;
        }
        return implode(PHP_EOL, $results);
    }

    /**
     * 获取状态描述
     * @param $status
     * @return string
     */
    protected function getStatusText($status)
    {
        static $texts = [
            Examination::STATUS_SUCCESS => '成功',
            Examination::STATUS_FAILED => '失败',
            Examination::STATUS_INTERRUPT => '中断',
            Examination::STATUS_WAITING => '等待'
        ];
        return isset($texts[$status]) ? $texts[$status] : '未知';
    }
    /**
     * 读取配置文件
     * @param $configFile
     * @return $this
     */
    protected function readConfigFile($configFile)
    {
        $config = new Config();
        return $config->load($configFile);
    }

    /**
     * 创建runner
     * @return Runner
     */
    protected function buildRunnerTask()
    {
        return Factory::createFromConfig($this->config);
    }
}