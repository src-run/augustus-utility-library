<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Utilities\Test\Transform\Argument\Expression\Archetype;

use SR\Utilities\Test\AbstractTest;
use SR\Utilities\Transform\Argument\Expression\Archetype\AbstractArchetype;

/**
 * @covers \SR\Utilities\Transform\Argument\Expression\Archetype\AbstractArchetype
 */
class AbstractArchetypeTest extends AbstractTest
{
    /**
     * @return AbstractArchetype|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getMockForTarget()
    {
        return $this
            ->getMockBuilder(AbstractArchetype::class)
            ->getMockForAbstractClass();
    }

    public function testSetAndGet()
    {
        $this->assertFalse($this->getMockForTarget()->isValid());
    }
}
