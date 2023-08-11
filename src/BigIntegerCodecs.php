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
 * Class BigIntegerCodecs
 * @package Charcoal\Adapters\GMP
 */
abstract class BigIntegerCodecs implements \Stringable
{
    /** @var \GMP */
    protected readonly \GMP $int;

    /**
     * @param int|string|\GMP $n
     */
    public function __construct(int|string|\GMP $n)
    {
        $this->int = $this->getGMPn($n);
    }

    /**
     * @param string $encodedStr
     * @param \Charcoal\Adapters\GMP\CustomBaseCharset $base
     * @return static
     */
    public static function fromCustomBase(string $encodedStr, CustomBaseCharset $base): static
    {
        if (!$base->caseSensitive) {
            $encodedStr = strtolower($encodedStr);
        }

        $len = strlen($encodedStr);
        $value = gmp_init(0, 10);
        $multiplier = gmp_init(1, 10);

        for ($i = $len - 1; $i >= 0; $i--) { // Start in reverse order
            $pos = gmp_mul($multiplier, gmp_init(strpos($base->charset, $encodedStr[$i]), 10));
            $value = gmp_add($value, $pos);
            $multiplier = gmp_mul($multiplier, $base->len);
        }

        return new static($value);
    }

    /**
     * @param \Charcoal\Adapters\GMP\CustomBaseCharset $base
     * @return string
     */
    public function toCustomBase(CustomBaseCharset $base): string
    {
        if (!$this->isUnsigned()) {
            throw new \InvalidArgumentException('Cannot convert a signed BigInteger to custom base');
        }

        $num = $this->int;
        $encoded = "";
        while (true) {
            if (gmp_cmp($num, $base->len) < 0) {
                break;
            }

            $pos = gmp_intval(gmp_mod($num, $base->len));
            $num = gmp_div($num, $base->len);
            $encoded = $base->charset[$pos] . $encoded;
        }

        if (gmp_cmp($num, 0) >= 0) {
            $encoded = $base->charset[gmp_intval($num)] . $encoded;
        }

        return $encoded;
    }

    /**
     * @param string $hex
     * @return static
     */
    public static function fromBase16(string $hex): static
    {
        // Validate string as Hexadecimal
        if (!preg_match('/^(0x)?[a-f0-9]+$/i', $hex)) {
            throw new \InvalidArgumentException('Cannot instantiate BigNumber; expected Hexadecimal string');
        }

        // Remove the "0x" prefix
        if (str_starts_with($hex, "0x")) {
            $hex = substr($hex, 2);
        }

        // Evens-out odd number of hexits
        if (strlen($hex) % 2 !== 0) {
            $hex = "0" . $hex;
        }

        return new static(gmp_init($hex, 16));
    }

    /**
     * @return string
     */
    public function toBase16(): string
    {
        $b16 = gmp_strval($this->int, 16);
        if (strlen($b16) % 2 !== 0) {
            $b16 = "0" . $b16;
        }

        return $b16;
    }

    /**
     * @param \Charcoal\Adapters\GMP\BuffersBridgeInterface $buffer
     * @return static
     */
    public static function fromBuffer(BuffersBridgeInterface $buffer): static
    {
        return new static(gmp_init($buffer->toBase16(), 16));
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
    public function toGMP(): \GMP
    {
        return $this->int;
    }

    /**
     * @param int|string|\Charcoal\Adapters\GMP\BigInteger|\Charcoal\Adapters\GMP\BuffersBridgeInterface|\GMP $n
     * @return \GMP
     */
    protected function getGMPn(int|string|self|BuffersBridgeInterface|\GMP $n): \GMP
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
                return gmp_init($n, 16);
            }

            throw new \InvalidArgumentException('Invalid/malformed value for BigInteger');
        }

        if ($n instanceof BuffersBridgeInterface) {
            return gmp_init($n->toBase16(), 16);
        }

        if ($n instanceof self) {
            return $n->toGMP();
        }

        throw new \OutOfBoundsException('Cannot use argument value with BigInteger');
    }
}