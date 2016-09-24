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

class NumberTransformTest extends AbstractTransformTest
{
    public function setUp()
    {
        $this->providerYmlFilePath = __DIR__.'/../Fixture/data-provider_transform-number.yml';
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
