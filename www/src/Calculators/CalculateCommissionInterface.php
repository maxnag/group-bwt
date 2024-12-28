<?php

namespace GroupBwt\TestTask\Calculators;

use GroupBwt\TestTask\Entities\TransactionEntity;

interface CalculateCommissionInterface
{
    /**
     * Calculate
     *
     * @param TransactionEntity $transaction
     *
     * @return void
     */
    public function calculate(TransactionEntity $transaction): void;

    /**
     * Get calculated commission result
     *
     * @return array<string|float>
     */
    public function getResult(): array;
}
