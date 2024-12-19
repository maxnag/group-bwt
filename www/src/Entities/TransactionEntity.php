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
     * @var int
     */
    private int $amount;

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
     * @param int $amount
     * @param string $currency
     * @param bool $isEU
     */
    public function __construct(int $bin, int $amount, string $currency, bool $isEU)
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
     * @return int
     */
    public function getAmount(): int
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
