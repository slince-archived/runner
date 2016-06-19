<?php
/**
 * slince template collector library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Runner\Command;

use Slince\Config\Config;
use Slince\Event\Dispatcher;
use Slince\Runner\Exception\RuntimeException;
use Slince\Runner\Factory;
use Slince\Runner\Runner;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

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
    }

    protected function bindEventsForUi(Runner $runner, OutputInterface $output)
    {
        $chain = $runner->getExaminationChain();
        $progressBar = new ProgressBar($output, count($chain));
        $progressBar->start();
        $dispatcher = $runner->getDispatcher();
        $dispatcher->bind(Runner::EVENT_RUN, function() use ($output, $progressBar){
            $output->writeln("测试工作启动，将要执行{$progressBar->getMaxSteps()}个任务");
        });
        //执行新的测试任务
        $dispatcher->bind(Runner::EVENT_EXAMINATION_EXECUTE, function() use($output, $progressBar){

        });
        //测试任务执行完毕
        $dispatcher->bind(Runner::EVENT_EXAMINATION_EXECUTE, function() use($output, $progressBar){
            $progressBar->advance(1);
        });
        $dispatcher->bind(Runner::EVENT_FINISH, function() use ($output, $progressBar){
            $progressBar->finish();
            $output->writeln("测试结束，正在生成测试报告");
        });
        $dispatcher->bind(Runner::EVENT_FINISH, function() use ($runner){
            $this->generateReport($runner);
        });
    }

    /**
     * 生成测试报告
     * @param Runner $runner
     */
    protected function generateReport(Runner $runner)
    {
        file_put_contents(getcwd() . "/report", '测试ok');
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

    protected function buildRunnerTask()
    {
        $runner = Factory::create();
        $chain = $runner->getExaminationChain();
        return $runner;
    }
}