<?php
/*
 * This file is a part of "charcoal-dev/gmp-adapter" package.
 * https://github.com/charcoal-dev/gmp-adapter
 *
 * Copyright (c) Furqan A. Siddiqui <hello@furqansiddiqui.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code or visit following link:
 * https://github.com/charcoal-dev/gmp-adapter/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Charcoal\Adapters\GMP;

/**
 * Class BigInteger
 * @package Charcoal\Adapters\GMP\BigInteger
 */
class BigInteger extends BigIntegerCodecs
{
    /**
     * @return bool
     */
    public function isUnsigned(): bool
    {
        return gmp_cmp($this->int, 0) >= 0;
    }

    /**
     * @return bool
     */
    public function isSigned(): bool
    {
        return !$this->isUnsigned();
    }

    /**
     * @param int|string|\Charcoal\Adapters\GMP\BigInteger|\Charcoal\Adapters\GMP\BuffersBridgeInterface|\GMP $n2
     * @return int
     */
    public function cmp(int|string|self|BuffersBridgeInterface|\GMP $n2): int
    {
        return gmp_cmp($this->int, $this->getGMPn($n2));
    }

    /**
     * @param int|string|\Charcoal\Adapters\GMP\BigInteger|\Charcoal\Adapters\GMP\BuffersBridgeInterface|\GMP $n2
     * @return bool
     */
    public function equals(int|string|self|BuffersBridgeInterface|\GMP $n2): bool
    {
        return $this->cmp($n2) === 0;
    }

    /**
     * @param int|string|\Charcoal\Adapters\GMP\BigInteger|\Charcoal\Adapters\GMP\BuffersBridgeInterface|\GMP $n2
     * @return bool
     */
    public function greaterThan(int|string|self|BuffersBridgeInterface|\GMP $n2): bool
    {
        return $this->cmp($n2) > 0;
    }

    /**
     * @param int|string|\Charcoal\Adapters\GMP\BigInteger|\Charcoal\Adapters\GMP\BuffersBridgeInterface|\GMP $n2
     * @return bool
     */
    public function greaterThanOrEquals(int|string|self|BuffersBridgeInterface|\GMP $n2): bool
    {
        return $this->cmp($n2) >= 0;
    }

    /**
     * @param int|string|\Charcoal\Adapters\GMP\BigInteger|\Charcoal\Adapters\GMP\BuffersBridgeInterface|\GMP $n2
     * @return bool
     */
    public function lessThan(int|string|self|BuffersBridgeInterface|\GMP $n2): bool
    {
        return $this->cmp($n2) < 0;
    }

    /**
     * @param int|string|\Charcoal\Adapters\GMP\BigInteger|\Charcoal\Adapters\GMP\BuffersBridgeInterface|\GMP $n2
     * @return bool
     */
    public function lessThanOrEquals(int|string|self|BuffersBridgeInterface|\GMP $n2): bool
    {
        return $this->cmp($n2) <= 0;
    }

    /**
     * @param int|string|\Charcoal\Adapters\GMP\BigInteger|\Charcoal\Adapters\GMP\BuffersBridgeInterface|\GMP $n2
     * @return $this
     */
    public function add(int|string|self|BuffersBridgeInterface|\GMP $n2): static
    {
        return new static(gmp_add($this->int, $this->getGMPn($n2)));
    }

    /**
     * @param int|string|\Charcoal\Adapters\GMP\BigInteger|\Charcoal\Adapters\GMP\BuffersBridgeInterface|\GMP $n2
     * @return $this
     */
    public function sub(int|string|self|BuffersBridgeInterface|\GMP $n2): static
    {
        return new static(gmp_sub($this->int, $this->getGMPn($n2)));
    }

    /**
     * @param int|string|\Charcoal\Adapters\GMP\BigInteger|\Charcoal\Adapters\GMP\BuffersBridgeInterface|\GMP $n2
     * @return $this
     */
    public function mul(int|string|self|BuffersBridgeInterface|\GMP $n2): static
    {
        return new static(gmp_mul($this->int, $this->getGMPn($n2)));
    }

    /**
     * @param int|string|\Charcoal\Adapters\GMP\BigInteger|\Charcoal\Adapters\GMP\BuffersBridgeInterface|\GMP $n2
     * @return $this
     */
    public function div(int|string|self|BuffersBridgeInterface|\GMP $n2): static
    {
        return new static(gmp_div($this->int, $this->getGMPn($n2)));
    }

    /**
     * @param int|string|\Charcoal\Adapters\GMP\BigInteger|\Charcoal\Adapters\GMP\BuffersBridgeInterface|\GMP $divisor
     * @return $this
     */
    public function mod(int|string|self|BuffersBridgeInterface|\GMP $divisor): static
    {
        return new static(gmp_mod($this->int, $this->getGMPn($divisor)));
    }

    /**
     * @param int|string|\Charcoal\Adapters\GMP\BigInteger|\Charcoal\Adapters\GMP\BuffersBridgeInterface|\GMP $n2
     * @return array|null
     */
    public function squareRoot(int|string|self|BuffersBridgeInterface|\GMP $n2): ?array
    {
        $n2 = $this->getGMPn($n2);
        if (gmp_legendre($this->int, $n2) !== 1) {
            return null;
        }

        $sqrt1 = gmp_powm($this->int, gmp_div_q(gmp_add($n2, gmp_init(1, 10)), gmp_init(4, 10)), $n2);
        $sqrt2 = gmp_mod(gmp_sub($n2, $sqrt1), $n2);
        return [new static($sqrt1), new static($sqrt2)];
    }

    /**
     * @param int $n
     * @return $this
     */
    public function shiftRight(int $n): static
    {
        return new static(gmp_div_q($this->int, gmp_pow(2, $n)));
    }

    /**
     * @param int $n
     * @return $this
     */
    public function shiftLeft(int $n): static
    {
        return new static(gmp_mul($this->int, gmp_pow(2, $n)));
    }
}

