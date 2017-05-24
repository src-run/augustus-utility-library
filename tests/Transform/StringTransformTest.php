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

class StringTransformTest extends AbstractTransformTest
{
    public function setUp()
    {
        $this->providerYmlFilePath = __DIR__.'/../Fixture/data-provider_transform-string.yml';
    }

    public function testToUpper()
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testToLower()
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testToAlphanumeric()
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testToAlphanumericAndDashes()
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testToAlphanumericAndSpacesToDashes()
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testToAlpha()
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testToNumeric()
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testSpacesToDashes()
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testDashesToSpaces()
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testSlugify()
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testCamelToSnakeCase()
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testCamelToPascalCase()
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testPascalToSnakeCase()
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testPascalToCamelCase()
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testSnakeToCamelCase()
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testSnakeToPascalCase()
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testToPhoneNumber()
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testToPhoneFormat()
    {
        $this->initRunner(__FUNCTION__);
    }

    protected function runnerAssertCustom($iteration, $input, $expect, $args, $target, $method, $instance, $callable)
    {
        $instance->set($input);
        $this->assertTrue($instance->isSame($input));

        $instance2 = $instance->copy();
        $instance2
            ->enableMutable()
            ->toUpper()
            ->spacesToDashes()
            ->toLower();

        $instance3 = $instance->spacesToDashes()->toLower();

        $this->assertSame($instance2->get(), $instance3->get());
        $this->assertTrue($instance2->isMutable());

        $instance2->disableMutable();

        $this->assertFalse($instance2->isMutable());
        $this->assertCount(strlen($instance->get()), $instance->split());
    }
}
