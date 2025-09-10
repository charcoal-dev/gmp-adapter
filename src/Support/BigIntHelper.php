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
     * Check if a value is within the range; accepts int|strings (any GMP supported base)
     */
    public static function inRange(int|string|\GMP $value, int|string|\GMP $min, int|string|\GMP $max): bool
    {
        $value = $value instanceof \GMP ? $value : gmp_init($value);
        $min = $min instanceof \GMP ? $min : gmp_init($min);
        $max = $max instanceof \GMP ? $max : gmp_init($max);
        if (gmp_cmp($min, $max) > 0) {
            throw new \InvalidArgumentException("Minimum value must be less than maximum value");
        }

        return gmp_cmp($value, $min) >= 0 && gmp_cmp($value, $max) <= 0;
    }
}