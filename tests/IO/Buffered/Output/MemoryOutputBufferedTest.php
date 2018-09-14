<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Tests\Utilities\IO\Buffered\Output;

use SR\Tests\Utilities\IO\Buffered\MemoryBufferedTestCase;
use SR\Utilities\IO\Buffered\BufferedInterface;
use SR\Utilities\IO\Buffered\Output\MemoryOutputBuffered;

/**
 * @covers \SR\Utilities\IO\Buffered\MemoryBufferedTrait
 * @covers \SR\Utilities\IO\Buffered\Output\MemoryOutputBuffered
 */
class MemoryOutputBufferedTest extends MemoryBufferedTestCase
{
    /**
     * @param float|null  $memory
     * @param string|null $mode
     *
     * @return BufferedInterface|MemoryOutputBuffered
     */
    protected static function createMemoryBufferedInstance(float $memory = null, string $mode = null): BufferedInterface
    {
        return new MemoryOutputBuffered($memory, $mode);
    }
}
