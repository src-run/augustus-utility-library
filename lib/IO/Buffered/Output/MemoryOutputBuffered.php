<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Utilities\IO\Buffered\Output;

use SR\Utilities\IO\Buffered\MemoryBufferedTrait;

final class MemoryOutputBuffered implements OutputBufferedInterface
{
    use MemoryBufferedTrait;

    /**
     * @param float|null  $memory Reference documentation for {@see self::setMemory()}}.
     * @param string|null $mode   Reference documentation for {@see self::setMode()}}.
     */
    public function __construct(float $memory = null, string $mode = null)
    {
        $this->setMode($mode ?? 'x+b');
        $this->setMemory($memory);
        $this->reset();
    }
}
