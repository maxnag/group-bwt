<?php

namespace Tests\Unit;

use GroupBwt\TestTask\CalculateCommission;
use GroupBwt\TestTask\Entities\RatesEntity;
use GroupBwt\TestTask\Entities\TransactionEntity;
use GroupBwt\TestTask\Providers\Bin\BinProviderInterface;
use GroupBwt\TestTask\Providers\ExchangeRate\ExchangeProviderInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionMethod;
use Tests\Unit\Providers\ProvidersTrait;
use Throwable;

/**
 * CalculateCommission unit test
 */
final class CalculateCommissionTest extends TestCase
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
            ['{"bin":"45717360","amount":"100.00","currency":"EUR"}', true, 1],
            ['{"bin":"516793","amount":"50.00","currency":"USD"}', false, 0.91],
            ['{"bin":"45417360","amount":"10000.00","currency":"JPY"}', false, 149.25],
            ['{"bin":"41417360","amount":"130.00","currency":"USD"}', false, 2.36],
            ['{"bin":"4745030","amount":"2000.00","currency":"GBP"}', false, 45.45],
        ];
    }

    /**
     *
     * @param string $rawData
     * @param bool $isEU
     * @param float $commission
     * @return void
     */
    #[DataProvider('additionProvider')]
    public function testCalculation(string $rawData, bool $isEU, float $commission): void
    {
        $data = json_decode($rawData);
        $rateEntity = new RatesEntity($data->currency, json_decode('{ "USD": 1.1, "EUR": 1, "JPY": 1.34, "GBP": 0.88 }'));

        $binProviderMock = $this->getMockBinProvider($isEU);
        $exchangeProviderMock = $this->getMockExchangeProvider($rateEntity);
        $calculateCommissionMock = $this->getMockBuilder(CalculateCommission::class)
            ->setConstructorArgs([$binProviderMock, $exchangeProviderMock])
            ->onlyMethods(['getTransactions'])
            ->getMock();

        $mockTransactions = [new TransactionEntity($data->bin, $data->amount, $data->currency, $isEU)];

        $calculateCommissionMock->method('getTransactions')->willReturn($mockTransactions);

        $this->assertEquals($calculateCommissionMock->calculate(''), $commission . PHP_EOL);
    }

    /**
     * Get transactions
     *
     * @return void
     * @throws Exception|ReflectionException
     */
    public function testGetTransactions(): void
    {
        $calculator = new CalculateCommission($this->getMockBinProvider(true), $this->createMock(ExchangeProviderInterface::class));

        $getTransactions = new ReflectionMethod(CalculateCommission::class, 'getTransactions');
        $getTransactions->setAccessible(true);

        $transactions = $getTransactions->invoke($calculator, __DIR__ . '/../../src/example.txt');

        $this->assertIsArray($transactions);
        $this->assertCount(5, $transactions);
        $this->assertInstanceOf(TransactionEntity::class, $transactions[0]);
        $this->assertEquals(45717360, $transactions[0]->getBin());
        $this->assertEquals(100.0, $transactions[0]->getAmount());
        $this->assertEquals('EUR', $transactions[0]->getCurrency());
        $this->assertTrue($transactions[0]->isEU());
    }

    /**
     * Get transactions with missing file
     *
     * @return void
     */
    public function testGetTransactionsFileMissing(): void
    {
        try {
            $calculator = new CalculateCommission($this->getMockBinProvider(true), $this->createMock(ExchangeProviderInterface::class));

            $getTransactions = new ReflectionMethod(CalculateCommission::class, 'getTransactions');
            $getTransactions->setAccessible(true);

            $getTransactions->invoke($calculator, '');
            self::fail('Expected exception not throw');
        } catch (Throwable $e) {
            $this->assertEquals('The file  doesn\'t exist', $e->getMessage());
        }
    }
}
