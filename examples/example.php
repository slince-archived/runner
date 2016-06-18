<?php
include __DIR__ . '/../vendor/autoload.php';

use Slince\Runner\Runner;
use Slince\Runner\ExaminationChain;
use Slince\Runner\Examination;
use Slince\Runner\Api;
use Slince\Runner\Assertion;
use Slince\Runner\Assertion\ResponseAssertion;

$api = new Api('http://www.qimuyu.com', 'GET', ['foo', 'bar']);
$examination = new Examination($api);

$responseAssertion = new ResponseAssertion();
$assertion = new Assertion($responseAssertion, 'isOk');

$examination->addAssertion($assertion);

$chain = new ExaminationChain();
$chain->enqueue($examination);
$runner = new Runner($chain);
$runner->run();
if ($runner->getStatus() == Runner::STATUS_WAITING) {
    
}