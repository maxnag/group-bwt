<?php

namespace GroupBwt\TestTask\Providers\ExchangeRate;

use Exception;
use GroupBwt\TestTask\Entities\RatesEntity;

/**
 * Exchange provider interface
 */
interface ExchangeProviderInterface
{
    /**
     * Must be implemented in each Exchange provider
     *
     * @return self
     */
    public function getRates(): self;

    /**
     * Get rate by currency
     *
     * @param string $currency
     *
     * @throws Exception
     *
     * @return RatesEntity
     */
    public function getRateByCurrency(string $currency): RatesEntity;
}
