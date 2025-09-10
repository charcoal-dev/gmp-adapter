<?php
/**
 * Part of the "charcoal-dev/gmp-adapter" package.
 * @link https://github.com/charcoal-dev/gmp-adapter
 */

declare(strict_types=1);

namespace Charcoal\Adapters\Gmp\Support;

/**
 * Helper class for working with big integers.
 */
final readonly class BigIntHelper
{
    /**
     * Check if a value is within the range, accepts int|strings (any GMP supported base)
     * Guarantees that values falling within the allowed range will be returned as decimal strings.
     */
    public static function inRange(int|string $value, int|string $min, int|string $max): false|string
    {
        $value = gmp_init($value, 10);
        $min = gmp_init($min, 10);
        $max = gmp_init($max, 10);
        if (gmp_cmp($min, $max) > 0) {
            throw new \InvalidArgumentException("Minimum value must be less than maximum value");
        }

        return gmp_cmp($value, $min) >= 0 && gmp_cmp($value, $max) <= 0 ?
            gmp_strval($value, 10) : false;
    }
}