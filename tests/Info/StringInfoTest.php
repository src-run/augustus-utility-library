<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Test\Info;

use SR\Test\AbstractTest;

class StringInfoTest extends AbstractTest
{
    public static $fixtureData = [
        'abcdef01234',
        '-----------',
        'abcd---1234',
        '--LMNOMQR@1',
        'jdE0@$@30cc',
        'The cow looked over the hill!',
    ];

    public function testSearchPosition()
    {
        $assertions = [
            'StringInfo::searchPosition' => [
                ['b', 1],
                ['-', 0],
                ['1', 7],
                ['@', 9],
                ['Z', null],
                [' over', 14],
            ],
        ];

        $this->runThroughAssertions($assertions);
    }

    public function testSearchPositionLeft()
    {
        $assertions = [
            'StringInfo::searchPositionFromLeft' => [
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
            'StringInfo::searchPositionFromRight' => [
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
            'StringInfo::contains' => [
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
}
