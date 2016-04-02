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

use SR\Utility\StringUtil;

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

    private function runThroughAssertions(array $assertions, array $fixtureData = null)
    {
        foreach ($assertions as $call => $opts) {
            $this->runThroughFixtureData($call, $opts, $fixtureData);
        }
    }

    private function runThroughFixtureData($callable, array $assert, array $fixtureData = null)
    {
        $fixtureData = $fixtureData === null ? self::$fixtureData : $fixtureData;

        foreach ($fixtureData as $i => $data) {
            $parameters = $assert[$i];
            array_unshift($parameters, $data);
            $expected = array_pop($parameters);
            $received = call_user_func_array('SR\Utility\\'.$callable, $parameters);
            $this->assertSame($expected, $received,
                'Call to '.$callable.' did not result in expectation of "'.$expected.'" with input "'.implode(",", $parameters).'": received "'.$received.'"');
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

    public function testToAlphanumeric()
    {
        $assertions = [
            'StringUtil::toAlphanumeric' => [
                ['abcdef01234'],
                [''],
                ['abcd1234'],
                ['LMNOMQR1',],
                ['jdE030cc'],
                ['Thecowlookedoverthehill']
            ]
        ];

        $this->runThroughAssertions($assertions);
    }

    public function testToAlphanumericAndDashes()
    {
        $assertions = [
            'StringUtil::toAlphanumericAndDashes' => [
                ['abcdef01234'],
                ['-----------'],
                ['abcd---1234'],
                ['--LMNOMQR1',],
                ['jdE030cc'],
                ['Thecowlookedoverthehill']
            ]
        ];

        $this->runThroughAssertions($assertions);
    }

    public function testSpacesToDashes()
    {
        $assertions = [
            'StringUtil::spacesToDashes' => [
                ['abcdef01234'],
                ['-'],
                ['abcd-1234'],
                ['-LMNOMQR@1'],
                ['jdE0@$@30cc'],
                ['The-cow-looked-over-the-hill!']
            ]
        ];

        $this->runThroughAssertions($assertions);

        $assertions = [
            'StringUtil::spacesToDashes' => [
                [false, 'abcdef01234'],
                [false, '-----------'],
                [false, 'abcd---1234'],
                [false, '--LMNOMQR@1'],
                [false, 'jdE0@$@30cc'],
                [false, 'The-cow-looked-over-the-hill!']
            ]
        ];

        $this->runThroughAssertions($assertions);
    }

    public function testDashesToSpaces()
    {
        $assertions = [
            'StringUtil::dashesToSpaces' => [
                ['abcdef01234'],
                [' '],
                ['abcd 1234'],
                [' LMNOMQR@1'],
                ['jdE0@$@30cc'],
                ['The cow looked over the hill!']
            ]
        ];

        $this->runThroughAssertions($assertions);

        $assertions = [
            'StringUtil::dashesToSpaces' => [
                [false, 'abcdef01234'],
                [false, '           '],
                [false, 'abcd   1234'],
                [false, '  LMNOMQR@1'],
                [false, 'jdE0@$@30cc'],
                [false, 'The cow looked over the hill!']
            ]
        ];

        $this->runThroughAssertions($assertions);
    }

    public function testToSlug()
    {
        $assertions = [
            'StringUtil::toSlug' => [
                ['abcdef01234'],
                ['-'],
                ['abcd-1234'],
                ['-lmnomqr1'],
                ['jde030cc'],
                ['the-cow-looked-over-the-hill']
            ]
        ];

        $this->runThroughAssertions($assertions);
    }

    public function testToPhoneNumber()
    {
        $fixtureData = [
            '1-222-333-4444',
            '222-333-4444',
            '(222) 333 4444',
            '(222) 333-4444',
            '+1 (222) 333-4444'
        ];

        $assertions = [
            'StringUtil::toPhoneNumber' => [
                ['12223334444'],
                ['12223334444'],
                ['12223334444'],
                ['12223334444'],
                ['12223334444'],
            ]
        ];

        $this->runThroughAssertions($assertions, $fixtureData);
    }

    public function testToPhoneNumberFormatted()
    {
        $fixtureData = [
            '12223334444',
            '2223334444',
            '1-222-333-4444',
            '222-333-4444',
            '(222) 333 4444',
            '(222) 333-4444',
            '+1 (222) 333-4444'
        ];

        $assertions = [
            'StringUtil::toPhoneNumberFormatted' => [
                ['+1 (222) 333-4444'],
                ['+1 (222) 333-4444'],
                ['+1 (222) 333-4444'],
                ['+1 (222) 333-4444'],
                ['+1 (222) 333-4444'],
                ['+1 (222) 333-4444'],
                ['+1 (222) 333-4444'],
            ]
        ];

        $this->runThroughAssertions($assertions, $fixtureData);

        $format = '+%COUNTRY% %NPA%-%CO%-%LINE%';
        $assertions = [
            'StringUtil::toPhoneNumberFormatted' => [
                [$format, '+1 222-333-4444'],
                [$format, '+1 222-333-4444'],
                [$format, '+1 222-333-4444'],
                [$format, '+1 222-333-4444'],
                [$format, '+1 222-333-4444'],
                [$format, '+1 222-333-4444'],
                [$format, '+1 222-333-4444'],
            ]
        ];

        $this->runThroughAssertions($assertions, $fixtureData);

        $format = '%NPA%-%CO%-%LINE%';
        $assertions = [
            'StringUtil::toPhoneNumberFormatted' => [
                [$format, '222-333-4444'],
                [$format, '222-333-4444'],
                [$format, '222-333-4444'],
                [$format, '222-333-4444'],
                [$format, '222-333-4444'],
                [$format, '222-333-4444'],
                [$format, '222-333-4444'],
            ]
        ];

        $this->runThroughAssertions($assertions, $fixtureData);
    }

    public function testCompare()
    {
        $fixtureData = [
            'abcdef0123',
            'ß',
            '漢字はユニコード',
            'ß', //no support for this yet...
            'ß', //sadly
            'abc',
            '012',
        ];

        $assertions = [
            'StringUtil::compare' => [
                ['abcdef0123', true],
                ['ß', true],
                ['漢字はユニコード', true],
                ['ss', false],
                ['sz', false],
                ['defg', false],
                ['abc', false]
            ]
        ];

        $this->runThroughAssertions($assertions, $fixtureData);
    }
}

/* EOF */
