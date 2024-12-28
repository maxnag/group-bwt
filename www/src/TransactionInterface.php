<?php

namespace GroupBwt\TestTask;

use Exception;
use GroupBwt\TestTask\Calculators\CalculateCommissionInterface;

interface TransactionInterface
{
    /**
     * Get calculate commission object
     *
     * @return CalculateCommissionInterface
     */
    public function getCalculationCommissionObject(): CalculateCommissionInterface;
}
