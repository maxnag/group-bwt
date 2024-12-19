<?php

namespace Tests\Unit\Providers;

use GroupBwt\TestTask\Entities\RatesEntity;
use GroupBwt\TestTask\Providers\Bin\BinProviderInterface;
use GroupBwt\TestTask\Providers\ExchangeRate\ExchangeProviderInterface;

/**
 * Providers Trait
 */
trait ProvidersTrait
{
    /**
     * Get Bin provider
     *
     * @param mixed $returnValue
     *
     * @return mixed
     */
    protected function getMockBinProvider(mixed $returnValue): mixed
    {
        $this->binProviderMock = $this->getMockBuilder(BinProviderInterface::class)->getMock();

        $this->binProviderMock->method('getBin')->willReturn($this->binProviderMock);
        $this->binProviderMock->method('isEU')->willReturn($returnValue);

        return $this->binProviderMock;
    }

    /**
     * Get exchange provider
     *
     * @param RatesEntity $returnValue
     *
     * @return mixed
     */
    protected function getMockExchangeProvider(RatesEntity $returnValue): mixed
    {
        $this->exchangeProviderMock = $this->getMockBuilder(ExchangeProviderInterface::class)->getMock();

        $this->exchangeProviderMock->method('getRates')->willReturn($this->exchangeProviderMock);
        $this->exchangeProviderMock->method('getRateByCurrency')->willReturn($returnValue);

        return $this->exchangeProviderMock;
    }
}
