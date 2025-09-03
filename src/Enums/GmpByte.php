<?php
/**
 * Part of the "charcoal-dev/gmp-adapter" package.
 * @link https://github.com/charcoal-dev/gmp-adapter
 */

declare(strict_types=1);

namespace Charcoal\Adapters\Gmp\Enums;

/**
 * An enum representing byte order (endianness) for packing and unpacking integers.
 */
enum GmpByte
{
    case LittleEndian;
    case BigEndian;

    /**
     * Packs a 64-bit integer into a string.
     */
    public function pack64(string $n): string
    {
        return str_pad(gmp_export(gmp_init($n, 10), 1, $this->gmpFlags()), 8, "\x00", STR_PAD_LEFT);
    }

    /**
     * Unpacks a 64-bit integer from a string.
     */
    public function unpack64(string $bn): \GMP
    {
        if (strlen($bn) !== 8) {
            throw new \LengthException("Input must be 8 bytes long");
        }

        return gmp_import($bn, 1, $this->gmpFlags());
    }

    /**
     * Determines the GMP flags based on the current instance.
     */
    public function gmpFlags(): int
    {
        return match ($this) {
            self::BigEndian => GMP_MSW_FIRST | GMP_BIG_ENDIAN,
            self::LittleEndian => GMP_LSW_FIRST | GMP_LITTLE_ENDIAN,
        };
    }
}