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
use SR\Utilities\StringQuery;

/**
 * @covers \SR\Utilities\Query\StringQuery
 */
class StringQueryTest extends AbstractUtilitiesTest
{
    public static $fixtureData = [
        'abcdef01234',
        '-----------',
        'abcd---1234',
        '--LMNOMQR@1',
        'jdE0@$@30cc',
        'The cow looked over the hill!',
    ];

    public function testSearchPositionLeft()
    {
        $assertions = [
            'StringQuery::searchPositionFromLeft' => [
                ['bcdef', 1],
                ['|', null],
                ['4', 10],
                ['M', 3],
                ['dE0@$@30cc', 1],
                ['e cow looked', 2],
            ],
        ];

        $this->runThroughAssertions($assertions);
    }

    public function testSearchPositionRight()
    {
        $assertions = [
            'StringQuery::searchPositionFromRight' => [
                ['bcdef', 1],
                ['|', null],
                ['4', 10],
                ['M', 6],
                ['dE0@$@30cc', 1],
                ['e cow looked', 2],
            ],
        ];

        $this->runThroughAssertions($assertions);
    }

    public function testSearchContains()
    {
        $assertions = [
            'StringQuery::contains' => [
                ['bcdef', true],
                ['|', false],
                ['4', true],
                ['--LMNOMQR@1', true],
                ['dE0@$@30CC', false],
                ['e cow looked', true],
            ],
        ];

        $this->runThroughAssertions($assertions);
    }

    /**
     * @group legacy
     * @group bcl
     */
    public function testDeprecatedNamespace(): void
    {
        $this->assertInstanceOf(StringQuery::class, new \SR\Util\Info\StringInfo());
        $this->assertInstanceOf(StringQuery::class, new \SR\Utilities\StringQuery());
    }
}
