<?php
/**
 * slince template collector library
 * @author Tao <taosikai@yeah.net>
 */

namespace Slince\Runner\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ValidateCommand extends Command
{
    function configure()
    {
        $this->setName('validate');
    }

    function execute(InputInterface $input, OutputInterface $output)
    {
        $configFile = $input->getOption('config');
    }
}