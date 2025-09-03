<?php
/**
 * Part of the "charcoal-dev/gmp-adapter" package.
 * @link https://github.com/charcoal-dev/gmp-adapter
 */

declare(strict_types=1);

namespace Charcoal\Adapters\Gmp\BaseConvert;

/**
 * This class is used to define a character set with an optional
 * case sensitivity flag. The length of the character set is
 * calculated upon instantiation.
 */
readonly class CustomBaseCharset
{
    public int $len;

    public function __construct(
        public string $charset,
        public bool   $caseSensitive
    )
    {
        $this->len = strlen($this->charset);
    }
}

