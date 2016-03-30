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
 * Class StringUtilTest.
 */
class StringUtilTest extends \PHPUnit_Framework_TestCase
{
    static public $fixtureData = [
        'abcdef01234',
        '-----------',
        'abcd---1234',
        '--LMNOMQR@1',
        'jdE0@$@30cc',
        'The cow looked over the hill!',
    ];

    private function runThroughAssertions(array $assertions)
    {
        foreach ($assertions as $call => $opts) {
            $this->runThroughFixtureData($call, $opts);
        }
    }

    private function runThroughFixtureData($callable, array $assert)
    {
        foreach (self::$fixtureData as $i => $data) {
            $parameters = $assert[$i];
            array_unshift($parameters, $data);
            $expected = array_pop($parameters);
            $received = call_user_func_array('SR\Utility\\'.$callable, $parameters);
            $this->assertSame($expected, $received, 'Call to '.$callable.' did not result in expectation of '.$expected);
        }
    }

    public function testSearchPositionDefault()
    {
        $assertions = [
            'StringUtil::searchPosition' => [
                ['b', 1],
                ['-', 0],
                ['1', 7],
                ['@', 9],
                ['Z', null],
                [' over', 14]
            ]
        ];

        $this->runThroughAssertions($assertions);
    }

    public function testSearchPositionLeft()
    {
        $assertions = [
            'StringUtil::searchPositionFromLeft' => [
                ['bcdef', 1],
                ['|', null],
                ['4', 10],
                ['M', 3],
                ['dE0@$@30cc', 1],
                ['e cow looked', 2]
            ]
        ];

        $this->runThroughAssertions($assertions);
    }

    public function testSearchPositionRight()
    {
        $assertions = [
            'StringUtil::searchPositionFromRight' => [
                ['bcdef', 1],
                ['|', null],
                ['4', 10],
                ['M', 6],
                ['dE0@$@30cc', 1],
                ['e cow looked', 2]
            ]
        ];

        $this->runThroughAssertions($assertions);
    }
}

/* EOF */
