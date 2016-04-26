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
 * Class IntegerTransformTest
 */
class IntegerTransformTest extends AbstractTest
{
    static public $fixtureData = [
        '0',
        '10',
        '20',
        '30',
        '30',
        '40',
        '50',
        '60',
    ];

    public function testIntegerTransformToBase()
    {
        $assertions = [
            'IntegerTransform::toBase' => [
                [100, 1000, 0],
                [100, 50, 5],
                [100, 999, 10, 199.80000000000001],
                [100, 1000, 1, 300.0],
                [100, 1000, 0, 300],
                [30, 100, null, true, 100],
                [100, 1000, null, true, 500],
                [0, 100, null],
            ]
        ];
        
        $this->expectException('\InvalidArgumentException');
        $this->expectExceptionMessage('Cannot convert from a base of zero.');

        $this->runThroughAssertions($assertions);
    }
}

/* EOF */
