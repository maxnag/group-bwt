<?php

namespace GroupBwt\TestTask\Entities;

use stdClass;

/**
 * Rates entity
 *
 * @group entity
 */
class RatesEntity
{
    /**
     * Currency
     *
     * @var string
     */
    private string $currency;

    /**
     * Rates
     *
     * @var stdClass
     */
    private stdClass $rates;

    public function __construct(string $currency, stdClass $rates)
    {
        $this->currency = $currency;
        $this->rates = $rates;
    }

    /**
     * Get rate
     *
     * @return float
     */
    public function getRate(): float
    {
        return (float) $this->rates->{$this->currency};
    }
}
