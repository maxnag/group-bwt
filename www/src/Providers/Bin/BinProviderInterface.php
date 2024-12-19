<?php

namespace GroupBwt\TestTask\Providers\Bin;

/**
 * BIN provider interface
 */
interface BinProviderInterface
{
    /**
     * Must be implemented in each BIN provider
     *
     * @param int $bin
     *
     * @return self
     */
    public function getBin(int $bin): self;

    /**
     * Checking belonging to EU countries
     *
     * works only with ISO 3166-1 alpha-2 format
     *
     * @return bool
     */
    public function isEU(): bool;
}
