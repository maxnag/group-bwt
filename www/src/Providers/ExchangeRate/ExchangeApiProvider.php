<?php

namespace GroupBwt\TestTask\Providers\ExchangeRate;

use Exception;
use GroupBwt\TestTask\Entities\RatesEntity;
use GroupBwt\TestTask\Providers\AbstractProvider;
use stdClass;

/**
 * Exchange API provider
 */
class ExchangeApiProvider extends AbstractProvider implements ExchangeProviderInterface
{
    /**
     * Rates data
     *
     * @var stdClass | null
     */
    protected stdClass|null $ratesData;

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
        $apiKey = $_ENV['API_EXCHANGE_KEY'] ?? throw new Exception('API_EXCHANGE_KEY is not set');

        $rawData = @call_user_func($this->httpHandler, str_replace('[apiKey]', $apiKey, $url));

        if ($rawData === false) {
            throw new Exception('Unable to get data from api.exchangeratesapi.io');
        }

        return $rawData;
    }

    /**
     * Get rates
     *
     * @throws Exception
     *
     * @return self
     */
    public function getRates(): self
    {
        // we cannot cache here due to rate changes at any time
        $rawData = $this->getData('https://api.exchangeratesapi.io/v1/latest?access_key=[apiKey]&format=1');

        // it remains here due to API request limitations, for debugging purposes
        // $rawData = '{ "rates": { "USD": 1.1, "EUR": 1, "JPY": 1.34, "GBP": 0.88 } }';

        $this->ratesData = json_decode($rawData);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON is not valid for rates. ' . json_last_error_msg());
        }

        return $this;
    }

    /**
     * Get rate by currency
     *
     * @param string $currency
     *
     * @throws Exception
     *
     * @return RatesEntity
     */
    public function getRateByCurrency(string $currency): RatesEntity
    {
        return new RatesEntity(
            $currency,
            !empty($this->ratesData->rates) && count(get_object_vars($this->ratesData->rates))
                ? $this->ratesData->rates
                : throw new Exception('Unable to get rates'),
        );
    }
}
