<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Util\Test\Transform\Argument\Expression\Archetype;

use SR\Util\Test\AbstractTest;
use SR\Util\Transform\Argument\Expression\Archetype\AbstractArchetype;

class AbstractArchetypeTest extends AbstractTest
{
    /**
     * @return AbstractArchetype
     */
    protected function getMockForTarget()
    {
        return $this->getMockBuilder(AbstractArchetype::class)
            ->getMockForAbstractClass();
    }

    public function testSetAndGet()
    {
        $archetype = $this->getMockForTarget();
        $this->assertFalse($archetype->isValid());
    }
}
