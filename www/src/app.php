<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use GroupBwt\TestTask\Calculators\CalculateCommission;
use GroupBwt\TestTask\Providers\Bin\BinProvider;
use GroupBwt\TestTask\Providers\ExchangeRate\ExchangeApiProvider;
use GroupBwt\TestTask\Transaction;

if (empty($argv[1])) {
    exit('Please, provide a file path after the script' . PHP_EOL);
}

if (file_exists(__DIR__ . '/../' . $argv[1]) === false) {
    exit("The file $argv[1] doesn't exist" . PHP_EOL);
}

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
