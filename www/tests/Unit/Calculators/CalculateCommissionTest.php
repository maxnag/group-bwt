<?php

namespace Tests\Unit\Calculators;

use GroupBwt\TestTask\Calculators\CalculateCommission;
use GroupBwt\TestTask\Entities\RatesEntity;
use GroupBwt\TestTask\Entities\TransactionEntity;
use GroupBwt\TestTask\Providers\Bin\BinProviderInterface;
use GroupBwt\TestTask\Providers\ExchangeRate\ExchangeProviderInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Providers\ProvidersTrait;

class CalculateCommissionTest extends TestCase
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
     * Data provider
     *
     * @return array<int, mixed>
     */
    public static function additionProvider(): array
    {
        return [
            [new TransactionEntity(45717360, 100.0, 'EUR', true), 1],
            [new TransactionEntity(516793, 50.0, 'USD', false), 0.91],
            [new TransactionEntity(45417360, 10000.0, 'JPY', false), 149.25],
            [new TransactionEntity(41417360, 130.0, 'USD', false), 2.36],
            [new TransactionEntity(4745030, 2000.0, 'GBP', false), 45.45],
        ];
    }

    /**
     * Calculate
     *
     * @param TransactionEntity $transactionEntity
     * @param float $commission
     *
     * @return void
     */
    #[DataProvider('additionProvider')]
    public function testCalculation(TransactionEntity $transactionEntity, float $commission): void
    {
        $_ENV['EU_RATE'] = 0.01;
        $_ENV['NON_EU_RATE'] = 0.02;

        $rateEntity = new RatesEntity($transactionEntity->getCurrency(), json_decode('{ "USD": 1.1, "EUR": 1, "JPY": 1.34, "GBP": 0.88 }'));
        $exchangeProviderMock = $this->getMockExchangeProvider($rateEntity);

        $calculator = new CalculateCommission($exchangeProviderMock);
        $calculator->calculate($transactionEntity);
        $result = $calculator->getResult();

        $this->assertCount(1, $result);
        $this->assertEquals($result[0], $commission);
    }

    /**
     * Get result
     *
     * @return void
     * @throws Exception
     */
    public function testGetResult(): void
    {
        $_ENV['EU_RATE'] = 0.1;
        $_ENV['NON_EU_RATE'] = 0.1;
        $calculator = new CalculateCommission($this->createMock(ExchangeProviderInterface::class));
        $result = $calculator->getResult();

        $this->assertCount(0, $result);
    }
}
