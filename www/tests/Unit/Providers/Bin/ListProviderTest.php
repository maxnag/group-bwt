<?php

namespace Tests\Unit\Providers\Bin;

use Exception;
use GroupBwt\TestTask\Providers\Bin\ListProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class ListProviderTest extends TestCase
{
    /**
     * Test getData with valid data
     *
     * @throws ReflectionException
     *
     * @return void
     */
    public function testGetDataReturnsValidData(): void
    {
        $mockHandler = fn($url) => '{"test": "data"}'; // Mock HTTP response

        $listProvider = new ListProvider($mockHandler);

        $reflection = new ReflectionMethod($listProvider, 'getData');
        $reflection->setAccessible(true);

        $response = $reflection->invoke($listProvider, 'https://example.com');

        $this->assertEquals('{"test": "data"}', $response);
    }

    /**
     * Test getData throws exception on failure
     *
     * @throws ReflectionException
     *
     * @return void
     */
    public function testGetDataThrowsException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unable to get data from binlist.net');

        $mockHandler = fn($url) => false;

        $listProvider = new ListProvider($mockHandler);

        $reflection = new ReflectionMethod($listProvider, 'getData');
        $reflection->setAccessible(true);

        $reflection->invoke($listProvider, 'https://example.com');
    }

    /**
     * Test getBin with valid data
     *
     * @throws ReflectionException
     *
     * @return void
     */
    public function testGetBinValidData(): void
    {
        $mockProvider = $this->getMockBuilder(ListProvider::class)
            ->onlyMethods(['getData'])
            ->getMock();

        $bin = 45717360;
        $validResponse = json_encode([
            'country' => ['alpha2' => 'US'],
        ]);

        $mockProvider
            ->expects($this->once())
            ->method('getData')
            ->with("https://lookup.binlist.net/$bin")
            ->willReturn($validResponse);

        $result = $mockProvider->getBin($bin);

        $reflection = new ReflectionClass($mockProvider);
        $property = $reflection->getProperty('countryAlpha2Code');
        $property->setAccessible(true);

        $this->assertEquals('US', $property->getValue($mockProvider));
        $this->assertInstanceOf(get_class($mockProvider), $result);
    }

    /**
     * Test getBin with invalid JSON
     *
     * @return void
     */
    public function testGetBinThrowsExceptionOnInvalidJson(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('JSON is not valid for BINs.');

        $mockProvider = $this->getMockBuilder(ListProvider::class)
            ->onlyMethods(['getData'])
            ->getMock();

        $bin = 45717360;

        $mockProvider
            ->expects($this->once())
            ->method('getData')
            ->with("https://lookup.binlist.net/$bin")
            ->willReturn('INVALID_JSON');

        $mockProvider->getBin($bin);
    }

    /**
     * Test getBin with missing country alpha2 code
     *
     * @throws ReflectionException
     *
     * @return void
     */
    public function testGetBinHandlesMissingCountryAlpha2Code(): void
    {
        $mockProvider = $this->getMockBuilder(ListProvider::class)
            ->onlyMethods(['getData'])
            ->getMock();

        $bin = 45717360;
        $response = json_encode([
            'country' => [], // No alpha2 code here
        ]);

        $mockProvider
            ->expects($this->once())
            ->method('getData')
            ->with("https://lookup.binlist.net/$bin")
            ->willReturn($response);

        $result = $mockProvider->getBin($bin);

        $reflection = new ReflectionClass($mockProvider);
        $property = $reflection->getProperty('countryAlpha2Code');
        $property->setAccessible(true);

        $this->assertEquals('', $property->getValue($mockProvider));
        $this->assertInstanceOf(get_class($mockProvider), $result);
    }
}
