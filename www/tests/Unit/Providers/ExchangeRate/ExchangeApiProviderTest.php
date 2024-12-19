<?php

namespace Tests\Unit\Providers\ExchangeRate;

use Exception;
use GroupBwt\TestTask\Entities\RatesEntity;
use GroupBwt\TestTask\Providers\ExchangeRate\ExchangeApiProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Throwable;

class ExchangeApiProviderTest extends TestCase
{
    /**
     * Test getData with a valid response
     *
     * @throws ReflectionException
     *
     * @return void
     */
    public function testGetDataReturnsValidData(): void
    {
        $mockHandler = function ($url) {
            return '{"rates": {"USD": 1.1, "EUR": 1}}';
        };

        $exchangeApiProvider = new ExchangeApiProvider($mockHandler);

        $reflection = new ReflectionMethod($exchangeApiProvider, 'getData');
        $reflection->setAccessible(true);

        $response = $reflection->invoke($exchangeApiProvider, 'https://api.exchangeratesapi.io/v1/latest?access_key=[apiKey]&format=1');

        $this->assertEquals('{"rates": {"USD": 1.1, "EUR": 1}}', $response);
    }

    /**
     * Test getData throws exception when API key is missing
     *
     * @throws ReflectionException
     *
     * @return void
     */
    public function testGetDataThrowsExceptionIfApiKeyIsMissing(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('API_EXCHANGE_KEY is not set');

        unset($_ENV['API_EXCHANGE_KEY']);

        $mockHandler = fn($url) => '{}';

        $exchangeApiProvider = new ExchangeApiProvider($mockHandler);

        $reflection = new ReflectionMethod($exchangeApiProvider, 'getData');
        $reflection->setAccessible(true);

        $reflection->invoke($exchangeApiProvider, 'https://api.exchangeratesapi.io/v1/latest?access_key=[apiKey]&format=1');
    }

    /**
     * Test getData throws exception on failure
     *
     * @throws ReflectionException
     *
     * @return void
     */
    public function testGetDataThrowsExceptionOnFailure(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unable to get data from api.exchangeratesapi.io');

        // Mock handler simulating failure
        $mockHandler = fn($url) => false;

        $_ENV['API_EXCHANGE_KEY'] = 'test';

        $exchangeApiProvider = new ExchangeApiProvider($mockHandler);

        $reflection = new ReflectionMethod($exchangeApiProvider, 'getData');
        $reflection->setAccessible(true);

        $reflection->invoke($exchangeApiProvider, 'https://api.exchangeratesapi.io/v1/latest?access_key=[apiKey]&format=1');
    }

    /**
     * Test getRates with valid data
     *
     * @throws Exception
     *
     * @return void
     */
    public function testGetRatesReturnsValidData(): void
    {
        $mockHandler = function ($url) {
            return '{"rates": {"USD": 1.1, "EUR": 1}}';
        };

        $exchangeApiProvider = new ExchangeApiProvider($mockHandler);

        $result = $exchangeApiProvider->getRates();

        $reflection = new ReflectionClass($exchangeApiProvider);
        $property = $reflection->getProperty('ratesData');
        $property->setAccessible(true);

        $ratesData = $property->getValue($exchangeApiProvider);

        $this->assertInstanceOf(ExchangeApiProvider::class, $result);
        $this->assertEquals((object) ['USD' => 1.1, 'EUR' => 1], $ratesData->rates);
    }

    /**
     * Test getRates throws exception on invalid JSON
     *
     * @return void
     */
    public function testGetRatesThrowsExceptionOnInvalidJson(): void
    {
        try {
            $mockHandler = function ($url) {
                return 'INVALID_JSON';
            };

            $exchangeApiProvider = new ExchangeApiProvider($mockHandler);

            $reflection = new ReflectionMethod($exchangeApiProvider, 'getData');
            $reflection->setAccessible(true);

            $exchangeApiProvider->getRates();
            self::fail('Expected exception not throw');
        } catch (Throwable $e) {
            $this->assertStringContainsString('JSON is not valid for rates', $e->getMessage());
        }
    }

    /**
     * Test getRateByCurrency with valid currency
     *
     * @throws Exception
     *
     * @return void
     */
    public function testGetRateByCurrencyReturnsValidEntity(): void
    {
        $mockHandler = function ($url) {
            return '{"rates": {"USD": 1.1, "EUR": 1}}';
        };

        $exchangeApiProvider = new ExchangeApiProvider($mockHandler);

        $exchangeApiProvider->getRates();

        $rateEntity = $exchangeApiProvider->getRateByCurrency('USD');

        $this->assertInstanceOf(RatesEntity::class, $rateEntity);
        $this->assertEquals(1.1, $rateEntity->getRate());
    }

    /**
     * Test getRateByCurrency throws exception when rates are missing
     *
     * @return void
     */
    public function testGetRateByCurrencyThrowsExceptionWhenRatesAreMissing(): void
    {
        try {
            $mockHandler = function ($url) {
                return '{"rates": {}}';
            };

            $exchangeApiProvider = new ExchangeApiProvider($mockHandler);

            $reflection = new ReflectionMethod($exchangeApiProvider, 'getData');
            $reflection->setAccessible(true);

            $exchangeApiProvider->getRates();

            $exchangeApiProvider->getRateByCurrency('USD');
            self::fail('Expected exception not throw');
        } catch (Throwable $e) {
            $this->assertStringContainsString('Unable to get rates', $e->getMessage());
        }
    }
}
