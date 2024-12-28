<?php

namespace GroupBwt\TestTask\Providers;

/**
 * Abstract provider class
 */
abstract class AbstractProvider
{
    /**
     * ISO 3166-1 alpha-2
     *
     * @link https://www.iso.org/iso-3166-country-codes.html
     * @var string
     */
    protected string $countryAlpha2Code = '';

    /**
     * Http Handler
     *
     * @var callable
     */
    protected $httpHandler;

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
     * Checking belonging to EU countries
     *
     * works only with ISO 3166-1 alpha-2 format
     *
     * @return bool
     */
    public function isEU(): bool
    {
        $euCountries = [
            'AT',
            'BE',
            'BG',
            'CY',
            'CZ',
            'DE',
            'DK',
            'EE',
            'ES',
            'FI',
            'FR',
            'GR',
            'HR',
            'HU',
            'IE',
            'IT',
            'LT',
            'LU',
            'LV',
            'MT',
            'NL',
            'PO',
            'PT',
            'RO',
            'SE',
            'SI',
            'SK',
        ];

        return in_array(strtoupper($this->countryAlpha2Code), $euCountries);
    }

    /**
     * Must be implemented in each BIN provider
     *
     * @param string $url
     *
     * @return string | false
     */
    abstract protected function getData(string $url): string|false;
}
