<?php
/**
 * Part of the "charcoal-dev/gmp-adapter" package.
 * @link https://github.com/charcoal-dev/gmp-adapter
 */

declare(strict_types=1);

namespace Charcoal\Adapters\Gmp\Traits;

use Charcoal\Adapters\Gmp\BaseConvert\CustomBaseCharset;
use Charcoal\Adapters\Gmp\Enums\GmpByte;
use Charcoal\Contracts\Buffers\ReadableBufferInterface;

/**
 * Provides utility methods for encoding and decoding big integer values
 * using various base representations and formats.
 */
trait BigIntegerCodecs
{
    /**
     * @param string $encodedStr
     * @param CustomBaseCharset $base
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
     * @param string $hex
     * @return static
     */
    public static function fromBase16(string $hex): static
    {
        if (str_starts_with($hex, "0x")) {
            $hex = substr($hex, 2);
        }

        if (!ctype_xdigit($hex)) {
            throw new \InvalidArgumentException('Cannot instantiate BigNumber; expected Hexadecimal string');
        }

        // Evens-out odd number of hexits
        if (strlen($hex) % 2 !== 0) {
            $hex = "0" . $hex;
        }

        return new static(gmp_init($hex, 16));
    }

    /**
     * @param ReadableBufferInterface $buffer
     * @param GmpByte $order
     * @return static
     */
    public static function fromBuffer(ReadableBufferInterface $buffer, GmpByte $order): static
    {
        return new static(gmp_import($buffer->bytes(), 1, $order->gmpFlags()));
    }

    /**
     * @return string
     */
    public function toBase16(): string
    {
        return self::encodeBase16($this->int);
    }

    /**
     * @param CustomBaseCharset $base
     * @return string
     */
    public function toCustomBase(CustomBaseCharset $base): string
    {
        if (!$this->isUnsigned()) {
            throw new \InvalidArgumentException("Cannot convert a signed BigInteger to custom base");
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
     * @param \GMP|ReadableBufferInterface $value
     * @return string
     */
    protected static function encodeBase16(\GMP|ReadableBufferInterface $value): string
    {
        $b16 = $value instanceof \GMP ? gmp_strval($value, 16) : bin2hex($value->bytes());
        if (strlen($b16) % 2 !== 0) {
            $b16 = "0" . $b16;
        }

        return $b16;
    }
}