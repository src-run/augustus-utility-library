<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 * (c) Scribe Inc      <scr@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Utility\Tests;

/**
 * Class ArrayInspectTest.
 */
class ArrayInspectTest extends AbstractTest
{
    public static $fixtureData = [
        ['one', 'two', 'three'],
        ['o' => 'one', 't' => 'two', 'h' => 'three'],
        ['one', 'two', 'three-index' => 'three'],
        [],
    ];

    public function testIsAssociative()
    {
        $assertions = [
            'ArrayInspect::isAssociative' => [
                [false],
                [true],
                [true],
                [null],
            ],
        ];

        $this->runThroughAssertions($assertions);
    }
}

/* EOF */
