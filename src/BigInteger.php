<?php
/**
 * Part of the "charcoal-dev/gmp-adapter" package.
 * @link https://github.com/charcoal-dev/gmp-adapter
 */

declare(strict_types=1);

namespace Charcoal\Adapters\GMP;

use Charcoal\Adapters\GMP\Contracts\BuffersBridgeInterface;

/**
 * Class BigInteger
 * @package Charcoal\Adapters\GMP\BigInteger
 */
class BigInteger extends BigIntegerCodecs
{
    public function isUnsigned(): bool
    {
        return gmp_cmp($this->int, 0) >= 0;
    }

    public function isSigned(): bool
    {
        return !$this->isUnsigned();
    }

    public function cmp(int|string|self|BuffersBridgeInterface|\GMP $n2): int
    {
        return gmp_cmp($this->int, $this->getGMPn($n2));
    }

    public function equals(int|string|self|BuffersBridgeInterface|\GMP $n2): bool
    {
        return $this->cmp($n2) === 0;
    }

    public function greaterThan(int|string|self|BuffersBridgeInterface|\GMP $n2): bool
    {
        return $this->cmp($n2) > 0;
    }

    public function greaterThanOrEquals(int|string|self|BuffersBridgeInterface|\GMP $n2): bool
    {
        return $this->cmp($n2) >= 0;
    }

    public function lessThan(int|string|self|BuffersBridgeInterface|\GMP $n2): bool
    {
        return $this->cmp($n2) < 0;
    }

    public function lessThanOrEquals(int|string|self|BuffersBridgeInterface|\GMP $n2): bool
    {
        return $this->cmp($n2) <= 0;
    }

    public function add(int|string|self|BuffersBridgeInterface|\GMP $n2): static
    {
        return new static(gmp_add($this->int, $this->getGMPn($n2)));
    }

    public function sub(int|string|self|BuffersBridgeInterface|\GMP $n2): static
    {
        return new static(gmp_sub($this->int, $this->getGMPn($n2)));
    }

    public function mul(int|string|self|BuffersBridgeInterface|\GMP $n2): static
    {
        return new static(gmp_mul($this->int, $this->getGMPn($n2)));
    }

    public function div(int|string|self|BuffersBridgeInterface|\GMP $n2): static
    {
        return new static(gmp_div($this->int, $this->getGMPn($n2)));
    }

    public function mod(int|string|self|BuffersBridgeInterface|\GMP $divisor): static
    {
        return new static(gmp_mod($this->int, $this->getGMPn($divisor)));
    }

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

    public function shiftRight(int $n): static
    {
        return new static(gmp_div_q($this->int, gmp_pow(2, $n)));
    }

    public function shiftLeft(int $n): static
    {
        return new static(gmp_mul($this->int, gmp_pow(2, $n)));
    }
}

