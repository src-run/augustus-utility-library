<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Utilities\Test;

use SR\Utilities\EngineQuery;

/**
 * @covers \SR\Utilities\EngineQuery
 */
class EngineQueryTest extends AbstractTest
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
            'EngineQuery::extensionLoaded' => [
                [true],
                [false],
                [true],
                [false],
            ],
        ];

        $this->runThroughAssertions($assertions);

        $this->expectException('\InvalidArgumentException');
        $this->expectExceptionMessage('No extensions provided for loaded check');

        EngineQuery::extensionLoaded();
    }
}
