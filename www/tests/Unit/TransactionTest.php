<?php

namespace Tests\Unit;

use GroupBwt\TestTask\Calculators\CalculateCommission;
use GroupBwt\TestTask\Entities\RatesEntity;
use GroupBwt\TestTask\Providers\Bin\BinProviderInterface;
use GroupBwt\TestTask\Providers\ExchangeRate\ExchangeProviderInterface;
use GroupBwt\TestTask\Transaction;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Tests\Unit\Providers\ProvidersTrait;

/**
 * Transaction unit test
 */
final class TransactionTest extends TestCase
{
    use ProvidersTrait;

    /**
     * BinProviderInterface
     *
     * @var BinProviderInterface|MockObject
     */
    protected BinProviderInterface|MockObject $binProviderMock;

    /**
     * ExchangeProviderInterface
     *
     * @var ExchangeProviderInterface|MockObject
     */
    protected ExchangeProviderInterface|MockObject $exchangeProviderMock;

    /**
     * Get transactions
     *
     * @return void
     * @throws Exception|ReflectionException
     */
    public function testGetTransactions(): void
    {
        $_ENV['EU_RATE'] = 0.01;
        $_ENV['NON_EU_RATE'] = 0.02;

        $rateEntity = new RatesEntity('EUR', json_decode('{ "USD": 1.1, "EUR": 1, "JPY": 1.34, "GBP": 0.88 }'));

        $transaction = new Transaction(
            new CalculateCommission($this->getMockExchangeProvider($rateEntity)),
            $this->getMockBinProvider(true),
            __DIR__ . '/../../src/example.txt',
        );

        $result = $transaction->getCalculationCommissionObject()->getResult();

        $this->assertCount(5, $result);
    }

    /**
     * Get calculation commission object
     *
     * @return void
     */
    public function testGetCalculationCommissionObject(): void
    {
        $_ENV['EU_RATE'] = 0.01;
        $_ENV['NON_EU_RATE'] = 0.02;

        $rateEntity = new RatesEntity('EUR', json_decode('{ "USD": 1.1, "EUR": 1, "JPY": 1.34, "GBP": 0.88 }'));

        $calculator = new Transaction(
            new CalculateCommission($this->getMockExchangeProvider($rateEntity)),
            $this->getMockBinProvider(true),
            'src/example.txt',
        );

        $this->assertInstanceOf(CalculateCommission::class, $calculator->getCalculationCommissionObject());
    }
}
