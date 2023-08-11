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
 * Interface BuffersBridgeInterface
 * @package Charcoal\Adapters\GMP
 */
interface BuffersBridgeInterface
{
    public function toBase16(): string;
}
