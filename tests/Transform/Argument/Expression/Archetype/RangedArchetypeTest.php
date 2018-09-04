<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Tests\Utilities\Transform\Argument\Expression\Archetype;

use SR\Tests\Utilities\AbstractUtilitiesTest;
use SR\Utilities\Transform\Argument\Expression\Archetype\RangedArchetype;

/**
 * @covers \SR\Utilities\Transform\Argument\Expression\Archetype\RangedArchetype
 */
class RangedArchetypeTest extends AbstractUtilitiesTest
{
    public function testSetAndGet()
    {
        $archetype = new RangedArchetype(null);
        $this->assertSame('[]', $archetype->get());

        $archetype->set('a-z');
        $this->assertSame('[a-z]', $archetype->get());

        $archetype->set('a-z', true);
        $this->assertSame('[^a-z]', $archetype->get());
    }
}
