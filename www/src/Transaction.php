<?php

namespace GroupBwt\TestTask;

use Exception;
use GroupBwt\TestTask\Calculators\CalculateCommissionInterface;
use GroupBwt\TestTask\Entities\TransactionEntity;
use GroupBwt\TestTask\Providers\Bin\BinProviderInterface;

/**
 * Work with transaction file
 */
class Transaction implements TransactionInterface
{
    /**
     * Construct
     *
     * @param CalculateCommissionInterface $calculateCommission
     * @param BinProviderInterface $binProvider
     * @param string $transactionFile
     */
    public function __construct(
        protected CalculateCommissionInterface $calculateCommission,
        protected BinProviderInterface $binProvider,
        protected string $transactionFile
    ) {
        try {
            $this->getTransactions();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Get calculate commission object
     *
     * @return CalculateCommissionInterface
     */
    public function getCalculationCommissionObject(): CalculateCommissionInterface
    {
        return $this->calculateCommission;
    }

    /**
     * Get transactions
     *
     * @throws Exception
     *
     * @return void
     */
    protected function getTransactions(): void
    {
        if (file_exists($this->transactionFile) === false) {
            throw new Exception("The file $this->transactionFile doesn't exist");
        }

        // better use fopen instead of file_get_contents
        // is more suitable for a big input file
        $fileHandle = fopen($this->transactionFile, 'r');

        if (!$fileHandle) {
            throw new Exception("Unable to open the file $this->transactionFile");
        }

        while (($row = fgets($fileHandle)) !== false) {
            $row = trim($row);

            if (empty($row)) {
                continue;
            }

            $decodedData = json_decode($row);

            if (json_last_error() !== JSON_ERROR_NONE) {
                continue;
            }

            $this->calculateCommission->calculate(new TransactionEntity(
                (int) $decodedData->bin,
                (int) $decodedData->amount,
                (string) $decodedData->currency,
                $this->binProvider->getBin((int) $decodedData->bin)->isEU()
            ));
        }

        fclose($fileHandle);
    }
}
