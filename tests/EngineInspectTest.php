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

use SR\Utility\EngineInspect;

/**
 * Class EngineInspectTest.
 */
class EngineInspectTest extends AbstractTest
{
    public static $fixtureData = [
        'mysqli',
        'invalid',
        'mysqli',
        'invalid',
    ];

    public function testLoaded()
    {
        $assertions = [
            'EngineInspect::extensionLoaded' => [
                [true],
                [false],
                [true],
                [false],
            ],
        ];

        $this->runThroughAssertions($assertions);

        $this->expectException('\InvalidArgumentException');
        $this->expectExceptionMessage('No extensions provided for loaded check');

        EngineInspect::extensionLoaded();
    }
}

/* EOF */
