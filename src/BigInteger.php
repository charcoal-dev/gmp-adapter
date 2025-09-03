<?php
/**
 * Part of the "charcoal-dev/gmp-adapter" package.
 * @link https://github.com/charcoal-dev/gmp-adapter
 */

declare(strict_types=1);

namespace Charcoal\Adapters\Gmp;

use Charcoal\Adapters\Gmp\Traits\BigIntegerCodecs;

/**
 * Represents an immutable arbitrary-precision integer.
 */
final readonly class BigInteger
{
    use BigIntegerCodecs;

    private \GMP $int;

    public function __construct(int|string|\GMP $n)
    {
        $this->int = $this->getGMPn($n);
    }

    /**
     * Checks if the current value is an unsigned integer.
     */
    public function isUnsigned(): bool
    {
        return gmp_cmp($this->int, 0) >= 0;
    }

    /**
     * Checks if the current value is a signed integer.
     */
    public function isSigned(): bool
    {
        return !$this->isUnsigned();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return gmp_strval($this->int, 10);
    }

    /**
     * @return int
     */
    public function toInt(): int
    {
        if ($this->cmp(PHP_INT_MAX) > 0) {
            throw new \OverflowException('Cannot convert BigInteger to int; Value too long');
        } elseif ($this->cmp(PHP_INT_MIN) < 0) {
            throw new \UnderflowException('Cannot convert BigInteger to int; Value too small');
        }

        return gmp_intval($this->int);
    }

    /**
     * @return \GMP
     */
    public function unwrap(): \GMP
    {
        return $this->int;
    }

    /**
     * -1 if the first value is less than the second value,
     * 0 if they are equal,
     * 1 if the first value is greater than the second value.
     */
    public function cmp(int|string|self|\GMP $n2): int
    {
        return gmp_cmp($this->int, $this->getGMPn($n2));
    }

    /**
     * Compare two values and return true if they are equal.
     */
    public function equals(int|string|self|\GMP $n2): bool
    {
        return $this->cmp($n2) === 0;
    }

    /**
     * Compare two values and return true if the first value is greater than the second value.
     */
    public function greaterThan(int|string|self|\GMP $n2): bool
    {
        return $this->cmp($n2) > 0;
    }

    /**
     * Compare two values and return true if the first value is greater than or equal to the second value.
     */
    public function greaterThanOrEquals(int|string|self|\GMP $n2): bool
    {
        return $this->cmp($n2) >= 0;
    }

    /**
     * Compare two values and return true if the first value is less than the second value.
     */
    public function lessThan(int|string|self|\GMP $n2): bool
    {
        return $this->cmp($n2) < 0;
    }

    /**
     * Compare two values and return true if the first value is less than or equal to the second value.
     */
    public function lessThanOrEquals(int|string|self|\GMP $n2): bool
    {
        return $this->cmp($n2) <= 0;
    }

    /**
     * Adds the specified value to the current value.
     * @return static
     */
    public function add(int|string|self|\GMP $n2): self
    {
        return new self(gmp_add($this->int, $this->getGMPn($n2)));
    }

    /**
     * Subtracts the specified value from the current value.
     * @return static
     */
    public function sub(int|string|self|\GMP $n2): self
    {
        return new self(gmp_sub($this->int, $this->getGMPn($n2)));
    }

    /**
     * Multiplies the current value by the specified factor.
     * @return static
     */
    public function mul(int|string|self|\GMP $n2): self
    {
        return new self(gmp_mul($this->int, $this->getGMPn($n2)));
    }

    /**
     * Divides the current value by the specified divisor.
     * @return static
     */
    public function div(int|string|self|\GMP $n2): self
    {
        return new self(gmp_div($this->int, $this->getGMPn($n2)));
    }

    /**
     * Calculates the remainder of the current value divided by the specified divisor.
     * @return static
     */
    public function mod(int|string|self|\GMP $divisor): self
    {
        return new self(gmp_mod($this->int, $this->getGMPn($divisor)));
    }

    /**
     * Calculates the square root of the current value.
     * @return array{static, static}|null
     */
    public function squareRoot(int|string|self|\GMP $n2): ?array
    {
        $n2 = $this->getGMPn($n2);
        if (gmp_legendre($this->int, $n2) !== 1) {
            return null;
        }

        $sqrt1 = gmp_powm($this->int, gmp_div_q(gmp_add($n2, gmp_init(1, 10)), gmp_init(4, 10)), $n2);
        $sqrt2 = gmp_mod(gmp_sub($n2, $sqrt1), $n2);
        return [new self($sqrt1), new self($sqrt2)];
    }

    /**
     * Shifts the current value to the right by the specified number of bits.
     * @return static
     */
    public function shiftRight(int $n): self
    {
        return new self(gmp_div_q($this->int, gmp_pow(2, $n)));
    }

    /**
     * Shifts the current value to the left by the specified number of bits.
     * @return static
     */
    public function shiftLeft(int $n): self
    {
        return new self(gmp_mul($this->int, gmp_pow(2, $n)));
    }

    /**
     * Converts the provided value to a \GMP object.
     */
    private function getGMPn(int|string|self|\GMP $n): \GMP
    {
        if ($n instanceof \GMP) {
            return $n;
        }

        if (is_int($n)) {
            return gmp_init($n, 10);
        }

        if (is_string($n)) {
            if (preg_match('/^(0|-?[1-9][0-9]+)$/', $n)) {
                return gmp_init($n, 10);
            } elseif (preg_match('/^(0x)?[a-f0-9]+$/i', $n)) {
                $n = preg_replace('/^0x/i', "", $n);
                return gmp_init($n, 16);
            }

            throw new \InvalidArgumentException('Invalid/malformed value for BigInteger');
        }

        if ($n instanceof self) {
            return $n->unwrap();
        }

        throw new \OutOfBoundsException('Cannot use argument value with BigInteger');
    }

}

