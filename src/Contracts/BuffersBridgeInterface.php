<?php
/**
 * Part of the "charcoal-dev/gmp-adapter" package.
 * @link https://github.com/charcoal-dev/gmp-adapter
 */

declare(strict_types=1);

namespace Charcoal\Adapters\GMP\Contracts;

/**
 * Interface BuffersBridgeInterface
 * @package Charcoal\Adapters\GMP\Contracts
 */
interface BuffersBridgeInterface
{
    public function toBase16(): string;
}
