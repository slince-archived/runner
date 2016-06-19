<?php
/**
 * slince template collector library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Runner\Command;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputOption;

class Command extends BaseCommand
{
    /**
     * 默认的配置文件名
     * @var string
     */
    const CONFIG_FILE = 'runner.json';

    /**
     * 配置文件option名称
     * @var string
     */
    const CONFIG_OPTION = 'config';

    function configure()
    {
        $this->addOption(self::CONFIG_OPTION, null, InputOption::VALUE_OPTIONAL, '配置文件',
            getcwd() . DIRECTORY_SEPARATOR . self::CONFIG_FILE
        );
    }

    function validateConfigFile($configFile)
    {

    }
}