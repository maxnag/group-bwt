<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use GroupBwt\TestTask\Calculators\CalculateCommission;
use GroupBwt\TestTask\Providers\Bin\BinProvider;
use GroupBwt\TestTask\Providers\ExchangeRate\ExchangeApiProvider;
use GroupBwt\TestTask\Transaction;

// env vars
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// processing
$transaction = new Transaction(
    new CalculateCommission(new ExchangeApiProvider()),
    new BinProvider(),
    $argv[1]
);

foreach ($transaction->getCalculationCommissionObject()->getResult() as $commission) {
    echo $commission . PHP_EOL;
}
