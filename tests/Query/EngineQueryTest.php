<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Tests\Utilities\Query;

use SR\Tests\Utilities\AbstractUtilitiesTest;
use SR\Utilities\Query\EngineQuery;

/**
 * @covers \SR\Utilities\Query\EngineQuery
 */
class EngineQueryTest extends AbstractUtilitiesTest
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

    /**
     * @group legacy
     * @group bcl
     */
    public function testDeprecatedNamespace(): void
    {
        $this->assertInstanceOf(EngineQuery::class, new \SR\Util\Info\EngineInfo());
        $this->assertInstanceOf(EngineQuery::class, new \SR\Utilities\EngineQuery());
    }
}
