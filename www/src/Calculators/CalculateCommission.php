<?php

namespace GroupBwt\TestTask\Calculators;

use Exception;
use GroupBwt\TestTask\Entities\TransactionEntity;
use GroupBwt\TestTask\Providers\ExchangeRate\ExchangeProviderInterface;

class CalculateCommission implements CalculateCommissionInterface
{
    /**
     * EU rate
     *
     * @var float
     */
    protected float $euRate;

    /**
     * Non EU rate
     *
     * @var float
     */
    protected float $nonEuRate;

    /**
     * Get rates
     *
     * @var ExchangeProviderInterface
     */
    protected ExchangeProviderInterface $rates;

    /**
     * Calculation results
     *
     * @var array<string|float>
     */
    protected array $result = [];

    /**
     * Constructor
     *
     * @param ExchangeProviderInterface $exchangeApiProvider
     */
    public function __construct(ExchangeProviderInterface $exchangeApiProvider)
    {
        try {
            $this->euRate = $_ENV['EU_RATE'] ?? throw new Exception('EU rate is not set');
            $this->nonEuRate = $_ENV['NON_EU_RATE'] ?? throw new Exception('Non EU rate is not set');
            $this->rates = $exchangeApiProvider->getRates();
        } catch (Exception $e) {
            exit($e->getMessage() . PHP_EOL);
        }
    }

    /**
     * Calculate
     *
     * @param TransactionEntity $transaction
     *
     * @return void
     */
    public function calculate(TransactionEntity $transaction): void
    {
        try {
            $currency = $transaction->getCurrency();
            $amount = $transaction->getAmount();
            $currentRate = $this->rates->getRateByCurrency($currency)->getRate();
            $currentResult = 0;

            if ($currency === 'EUR' || $currentRate == 0) {
                $currentResult = $amount;
            }

            if ($currency !== 'EUR' || $currentRate > 0) {
                $currentResult = $amount / $currentRate;
            }

            $this->result[] = round($currentResult * ($transaction->isEU() ? $this->euRate : $this->nonEuRate), 2);
        } catch (Exception $e) {
            $this->result = [$e->getMessage()];
        }
    }

    /**
     * Get calculated commission result
     *
     * @return array<string|float>
     */
    public function getResult(): array
    {
        return $this->result;
    }
}
