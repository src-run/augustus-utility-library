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
use SR\Utilities\Query\ArrayQuery;

/**
 * @covers \SR\Utilities\Query\ArrayQuery
 */
class ArrayQueryTest extends AbstractUtilitiesTest
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
            'ArrayQuery::isAssociative' => [
                [false],
                [true],
                [true],
                [null],
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
        $this->assertInstanceOf(ArrayQuery::class, new \SR\Util\Info\ArrayInfo());
        $this->assertInstanceOf(ArrayQuery::class, new \SR\Utilities\ArrayQuery());
    }
}
