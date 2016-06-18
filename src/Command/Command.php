<?php
/**
 * slince template collector library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Runner\Command;

use Symfony\Component\Console\Command\Command as BaseCommand;

class Command extends BaseCommand
{
    /**
     * 默认的配置文件名
     * var string
     */
    const CONFIG_FILE = 'runner.json';

    function configure()
    {
        $this->addOption('config', null, InputOption::VALUE_OPTIONAL, '配置文件', getcwd() . self::CONFIG_FILE);
    }
}