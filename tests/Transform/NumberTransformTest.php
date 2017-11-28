<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Util\Test\Transform;

use SR\Util\Transform\NumberTransform;

class NumberTransformTest extends AbstractTransformTest
{
    /**
     * @var string
     */
    protected const FIXTURE_FILE = 'fixture_transform-number.yml';

    /**
     * @return \Generator
     */
    public function provideTestNumberTypeData() : \Generator
    {
        yield [100, 'isInteger'];
        yield ['100', 'isInteger'];
        yield [100.50, 'isFloat'];
        yield ['100.50', 'isFloat'];
    }

    /**
     * @dataProvider provideTestNumberTypeData
     *
     * @param int|float|string $provided
     * @param string           $method
     *
     * @return void
     */
    public function testNumberType($provided, string $method)
    {
        $this->assertTrue(call_user_func([new NumberTransform($provided), $method]));
    }

    /**
     * @return \Generator
     */
    public function provideTestMutatorAndAccessorData() : \Generator
    {
        yield [100, 100];
        yield ['100', 100];
        yield [100.50, 100.50];
        yield ['100.50', 100.50];
    }

    /**
     * @return \Generator
     */
    public function provideTestConstructorExceptionOnInvalidValueData() : \Generator
    {
        yield [new \stdClass()];
    }

    public function testToInteger()
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testToFloat()
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testRound()
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testIncrement()
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testDecrement()
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testMultiply()
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testDivide()
    {
        $this->initRunner(__FUNCTION__);
    }
}
