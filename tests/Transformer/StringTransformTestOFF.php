<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Test\Transformer;

use SR\Test\AbstractTest;

class StringTransformTest extends AbstractTest
{
    public static $fixtureData = [
        'abcdef01234',
        '-----------',
        'abcd---1234',
        '--LMNOMQR@1',
        'jdE0@$@30cc',
        'The cow looked over the hill!',
    ];

    public static $fixtureCamelCase = [
        'stringFixture',
        'secondStringFixtureExample',
        'anExampleWithABackToBackUpperCharacter',
        'aBCDEF',
    ];

    public static $fixturePascalCase = [
        'StringFixture',
        'SecondStringFixtureExample',
        'AnExampleWithABackToBackUpperCharacter',
        'ABCDEF',
    ];

    public static $fixtureSnakeCase = [
        'string_fixture',
        'second_string_fixture_example',
        'an_example_with_a_back_to_back_upper_character',
        'a_b_c_d_e_f',
    ];

    public function testToAlphanumeric()
    {
        $assertions = [
            'StringTransform::toAlphanumeric' => [
                ['abcdef01234'],
                [''],
                ['abcd1234'],
                ['LMNOMQR1'],
                ['jdE030cc'],
                ['Thecowlookedoverthehill'],
            ],
        ];

        $this->runThroughAssertions($assertions);
    }

    public function testToAlphanumericAndDashes()
    {
        $assertions = [
            'StringTransform::toAlphanumericAndDashes' => [
                ['abcdef01234'],
                ['-----------'],
                ['abcd---1234'],
                ['--LMNOMQR1'],
                ['jdE030cc'],
                ['Thecowlookedoverthehill'],
            ],
        ];

        $this->runThroughAssertionsAsInstance($assertions);
    }

    public function testSpacesToDashes()
    {
        $assertions = [
            'StringTransform::spacesToDashes' => [
                ['abcdef01234'],
                ['-'],
                ['abcd-1234'],
                ['-LMNOMQR@1'],
                ['jdE0@$@30cc'],
                ['The-cow-looked-over-the-hill!'],
            ],
        ];

        $this->runThroughAssertions($assertions);

        $assertions = [
            'StringTransform::spacesToDashes' => [
                [false, 'abcdef01234'],
                [false, '-----------'],
                [false, 'abcd---1234'],
                [false, '--LMNOMQR@1'],
                [false, 'jdE0@$@30cc'],
                [false, 'The-cow-looked-over-the-hill!'],
            ],
        ];

        $this->runThroughAssertions($assertions);
    }

    public function testDashesToSpaces()
    {
        $assertions = [
            'StringTransform::dashesToSpaces' => [
                ['abcdef01234'],
                [' '],
                ['abcd 1234'],
                [' LMNOMQR@1'],
                ['jdE0@$@30cc'],
                ['The cow looked over the hill!'],
            ],
        ];

        $this->runThroughAssertions($assertions);

        $assertions = [
            'StringTransform::dashesToSpaces' => [
                [false, 'abcdef01234'],
                [false, '           '],
                [false, 'abcd   1234'],
                [false, '  LMNOMQR@1'],
                [false, 'jdE0@$@30cc'],
                [false, 'The cow looked over the hill!'],
            ],
        ];

        $this->runThroughAssertions($assertions);
    }

    public function testToSlug()
    {
        $assertions = [
            'StringTransform::toSlug' => [
                ['abcdef01234'],
                ['-'],
                ['abcd-1234'],
                ['-lmnomqr1'],
                ['jde030cc'],
                ['the-cow-looked-over-the-hill'],
            ],
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
            '+1 (222) 333-4444',
        ];

        $assertions = [
            'StringTransform::toPhoneNumber' => [
                ['12223334444'],
                ['12223334444'],
                ['12223334444'],
                ['12223334444'],
                ['12223334444'],
            ],
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
            '+1 (222) 333-4444',
        ];

        $assertions = [
            'StringTransform::toPhoneNumberFormatted' => [
                ['+1 (222) 333-4444'],
                ['+1 (222) 333-4444'],
                ['+1 (222) 333-4444'],
                ['+1 (222) 333-4444'],
                ['+1 (222) 333-4444'],
                ['+1 (222) 333-4444'],
                ['+1 (222) 333-4444'],
            ],
        ];

        $this->runThroughAssertions($assertions, $fixtureData);

        $format = '+%COUNTRY% %NPA%-%CO%-%LINE%';
        $assertions = [
            'StringTransform::toPhoneNumberFormatted' => [
                [$format, '+1 222-333-4444'],
                [$format, '+1 222-333-4444'],
                [$format, '+1 222-333-4444'],
                [$format, '+1 222-333-4444'],
                [$format, '+1 222-333-4444'],
                [$format, '+1 222-333-4444'],
                [$format, '+1 222-333-4444'],
            ],
        ];

        $this->runThroughAssertions($assertions, $fixtureData);

        $format = '%NPA%-%CO%-%LINE%';
        $assertions = [
            'StringTransform::toPhoneNumberFormatted' => [
                [$format, '222-333-4444'],
                [$format, '222-333-4444'],
                [$format, '222-333-4444'],
                [$format, '222-333-4444'],
                [$format, '222-333-4444'],
                [$format, '222-333-4444'],
                [$format, '222-333-4444'],
            ],
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
            'StringTransform::compare' => [
                ['abcdef0123', true],
                ['ß', true],
                ['漢字はユニコード', true],
                ['ss', false],
                ['sz', false],
                ['defg', false],
                ['abc', false],
            ],
        ];

        $this->runThroughAssertions($assertions, $fixtureData);
    }

    /**
     * @param array $array
     *
     * @return array
     */
    private function fixtureToAssertionExpectations(array $array)
    {
        array_walk($array, function (&$element) {
            $element = [$element];
        });

        return $array;
    }

    public function testCamelToSnakeCase()
    {
        $parameters = self::$fixtureCamelCase;
        $assertions = [
            'StringTransform::camelToSnakeCase' => $this->fixtureToAssertionExpectations(self::$fixtureSnakeCase),
        ];

        $this->runThroughAssertions($assertions, $parameters);
    }

    public function testCamelToPascalCase()
    {
        $parameters = self::$fixtureCamelCase;
        $assertions = [
            'StringTransform::camelToPascalCase' => $this->fixtureToAssertionExpectations(self::$fixturePascalCase),
        ];

        $this->runThroughAssertions($assertions, $parameters);
    }

    public function testPascalToSnakeCase()
    {
        $parameters = self::$fixturePascalCase;
        $assertions = [
            'StringTransform::pascalToSnakeCase' => $this->fixtureToAssertionExpectations(self::$fixtureSnakeCase),
        ];

        $this->runThroughAssertions($assertions, $parameters);
    }

    public function testPascalToCamelCase()
    {
        $parameters = self::$fixturePascalCase;
        $assertions = [
            'StringTransform::pascalToCamelCase' => $this->fixtureToAssertionExpectations(self::$fixtureCamelCase),
        ];

        $this->runThroughAssertions($assertions, $parameters);
    }

    public function testSnakeToCamelCase()
    {
        $parameters = self::$fixtureSnakeCase;
        $assertions = [
            'StringTransform::snakeToCamelCase' => $this->fixtureToAssertionExpectations(self::$fixtureCamelCase),
        ];

        $this->runThroughAssertions($assertions, $parameters);
    }

    public function testSnakeToPascalCase()
    {
        $parameters = self::$fixtureSnakeCase;
        $assertions = [
            'StringTransform::snakeToPascalCase' => $this->fixtureToAssertionExpectations(self::$fixturePascalCase),
        ];

        $this->runThroughAssertions($assertions, $parameters);
    }
}
