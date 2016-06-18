<?php
/**
 * slince runner library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Runner\Assertion;

use GuzzleHttp\Psr7\Response;

interface AssertionInterface
{
    function execute(Response $response);
}