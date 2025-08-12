<?php
/**
 * Part of the "charcoal-dev/gmp-adapter" package.
 * @link https://github.com/charcoal-dev/gmp-adapter
 */

declare(strict_types=1);

namespace Charcoal\Adapters\GMP;

/**
 * Class CustomBaseCharset
 * @package Charcoal\Adapters\GMP
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

