<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use GroupBwt\TestTask\CalculateCommission;
use GroupBwt\TestTask\Providers\Bin\ListProvider;
use GroupBwt\TestTask\Providers\ExchangeRate\ExchangeApiProvider;

// env vars
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// processing
echo (new CalculateCommission(new ListProvider(), new ExchangeApiProvider()))->calculate($argv[1]);
