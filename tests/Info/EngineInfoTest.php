<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Util\Test\Info;

use SR\Util\Info\EngineInfo;
use SR\Util\Test\AbstractTest;

class EngineInfoTest extends AbstractTest
{
    public static $fixtureData = [
        'PDO',
        'invalid',
        'PDO',
        'invalid',
    ];

    public function testLoaded()
    {
        $assertions = [
            'EngineInfo::extensionLoaded' => [
                [true],
                [false],
                [true],
                [false],
            ],
        ];

        $this->runThroughAssertions($assertions);

        $this->expectException('\InvalidArgumentException');
        $this->expectExceptionMessage('No extensions provided for loaded check');

        EngineInfo::extensionLoaded();
    }
}
