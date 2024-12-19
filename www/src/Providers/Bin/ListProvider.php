<?php

namespace GroupBwt\TestTask\Providers\Bin;

use Exception;
use GroupBwt\TestTask\Providers\AbstractProvider;

/**
 * BIN list provider
 */
class ListProvider extends AbstractProvider implements BinProviderInterface
{
    /**
     * Http Handler
     *
     * @var callable
     */
    private $httpHandler;

    /**
     * Constructor to set custom HTTP handler
     *
     * @param callable|null $httpHandler
     */
    public function __construct(callable $httpHandler = null)
    {
        $this->httpHandler = $httpHandler ?: 'file_get_contents';
    }

    /**
     * Get data
     *
     * @param string $url
     *
     * @throws Exception
     *
     * @return string|false
     */
    protected function getData(string $url): string|false
    {
        $rawData = @call_user_func($this->httpHandler, $url);

        if ($rawData === false) {
            throw new Exception('Unable to get data from binlist.net');
        }

        return $rawData;
    }

    /**
     * Get BIN
     *
     * @param int $bin
     *
     * @throws Exception
     *
     * @return $this
     */
    public function getBin(int $bin): self
    {
        $rawData = $this->getData("https://lookup.binlist.net/$bin");

        // it remains here due to API request limitation
        /*$rawData = '{
          "number": {
            "length": 16,
            "luhn": true
          },
          "scheme": "visa",
          "type": "debit",
          "brand": "Visa/Dankort",
          "prepaid": false,
          "country": {
            "numeric": "208",
            "alpha2": "DK",
            "name": "Denmark",
            "emoji": "🇩🇰",
            "currency": "DKK",
            "latitude": 56,
            "longitude": 10
          },
          "bank": {
            "name": "Jyske Bank",
            "url": "www.jyskebank.dk",
            "phone": "+4589893300",
            "city": "Hjørring"
          }
        }
        ';*/

        $binData = json_decode($rawData);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON is not valid for BINs. ' . json_last_error_msg());
        }

        $this->countryAlpha2Code = !empty($binData->country->alpha2) ? strtoupper($binData->country->alpha2) : '';

        return $this;
    }
}