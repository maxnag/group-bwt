<?php

namespace GroupBwt\TestTask;

use Exception;
use GroupBwt\TestTask\Entities\TransactionEntity;
use GroupBwt\TestTask\Providers\Bin\BinProviderInterface;
use GroupBwt\TestTask\Providers\ExchangeRate\ExchangeProviderInterface;

/**
 * Calculate commission
 */
class CalculateCommission
{
    /**
     * BIN provider
     *
     * @var BinProviderInterface
     */
    protected BinProviderInterface $binProvider;

    /**
     * Exchange provider
     *
     * @var ExchangeProviderInterface
     */
    protected ExchangeProviderInterface $exchangeApiProvider;

    /**
     * Constructor
     *
     * @param BinProviderInterface $binProvider
     * @param ExchangeProviderInterface $exchangeApiProvider
     */
    public function __construct(BinProviderInterface $binProvider, ExchangeProviderInterface $exchangeApiProvider)
    {
        $this->binProvider = $binProvider;
        $this->exchangeApiProvider = $exchangeApiProvider;
    }

    /**
     * Calculate
     *
     * @param string $argv
     *
     * @return string
     */
    public function calculate(string $argv): string
    {
        try {
            $rates = $this->exchangeApiProvider->getRates();
            $output = '';

            foreach ($this->getTransactions($argv) as $transaction) {
                $currency = $transaction->getCurrency();
                $amount = $transaction->getAmount();
                $currentRate = $rates->getRateByCurrency($currency)->getRate();
                $result = 0;

                if ($currency === 'EUR' || $currentRate == 0) {
                    $result = $amount;
                }

                if ($currency !== 'EUR' || $currentRate > 0) {
                    $result = $amount / $currentRate;
                }

                $output .= round($result * ($transaction->isEU() ? 0.01 : 0.02), 2) . PHP_EOL;
            }

            return $output;
        } catch (Exception $e) {
            return $e->getMessage() . PHP_EOL;
        }
    }

    /**
     * Get transactions
     *
     * @param string $argv
     *
     * @throws Exception
     *
     * @return TransactionEntity[]
     */
    protected function getTransactions(string $argv): array
    {
        $transactions = [];

        if (file_exists($argv) === false) {
            throw new Exception("The file $argv doesn't exist");
        }

        // better use fopen instead of file_get_contents
        // is more suitable for a big input file
        $fileHandle = fopen($argv, 'r');

        if (!$fileHandle) {
            throw new Exception("Unable to open the file $argv");
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

            $transactions[] = new TransactionEntity(
                (int) $decodedData->bin,
                (int) $decodedData->amount,
                (string) $decodedData->currency,
                $this->binProvider->getBin((int) $decodedData->bin)->isEU()
            );
        }

        fclose($fileHandle);

        return $transactions;
    }
}
