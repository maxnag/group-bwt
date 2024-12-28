<?php

namespace GroupBwt\TestTask\Entities;

/**
 * Transaction entity
 *
 * @group entity
 */
class TransactionEntity
{
    /**
     * BIN
     *
     * @var int
     */
    private int $bin;

    /**
     * Amount
     *
     * @var float
     */
    private float $amount;

    /**
     * Currency
     *
     * @var string
     */
    private string $currency;

    /**
     * From EU country
     *
     * @var bool
     */
    private bool $isEU;

    /**
     * Construct
     *
     * @param int $bin
     * @param float $amount
     * @param string $currency
     * @param bool $isEU
     */
    public function __construct(int $bin, float $amount, string $currency, bool $isEU)
    {
        $this->bin = $bin;
        $this->amount = $amount;
        $this->currency = strtoupper($currency);
        $this->isEU = $isEU;
    }

    /**
     * Get BIN
     *
     * @return int
     */
    public function getBin(): int
    {
        return $this->bin;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * Check country is EU?
     *
     * @return bool
     */
    public function isEU(): bool
    {
        return $this->isEU;
    }
}
