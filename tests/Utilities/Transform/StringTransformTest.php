<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Utilities\Test\Transform;

use SR\Utilities\Test\Loader\Model\Package;

/**
 * @covers \SR\Utilities\Transform\Argument\Expression\Archetype\AbstractArchetype
 * @covers \SR\Utilities\Transform\Argument\Expression\Archetype\RangedArchetype
 * @covers \SR\Utilities\Transform\Argument\Expression\Archetype\StringArchetype
 * @covers \SR\Utilities\Transform\Argument\Expression\Representative\AbstractRepresentative
 * @covers \SR\Utilities\Transform\Argument\Expression\Representative\SearchReplaceRepresentative
 * @covers \SR\Utilities\Transform\Argument\Expression\Representative\SearchRepresentative
 * @covers \SR\Utilities\Transform\AbstractTransform
 * @covers \SR\Utilities\Transform\StringTransform
 */
class StringTransformTest extends AbstractTransformTest
{
    /**
     * @var string
     */
    protected const FIXTURE_FILE = 'fixture_transform-string.yml';

    /**
     * @return \Generator
     */
    public function provideTestMutatorAndAccessorData(): \Generator
    {
        yield ['string', 'string'];
        yield [100, '100'];
        yield [100.5, '100.5'];
        yield [(new class() {
            public function __toString(): string
            {
                return 'string';
            }
        }), 'string'];
    }

    /**
     * @return \Generator
     */
    public function provideTestConstructorExceptionOnInvalidValueData(): \Generator
    {
        yield [new class() {
        }];
    }

    /* comparisons */

    public function testIsSame(): void
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testIsNotSame(): void
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testIsEqual(): void
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testIsNotEqual(): void
    {
        $this->initRunner(__FUNCTION__);
    }

    /* case */

    public function testToUpper(): void
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testToLower(): void
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testToAlphanumeric(): void
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testToAlphanumericAndDashes(): void
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testToAlphanumericAndSpacesToDashes(): void
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testToAlpha(): void
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testToNumeric(): void
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testSpacesToDashes(): void
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testDashesToSpaces(): void
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testSlugify(): void
    {
        $this->initRunner(__FUNCTION__);
    }

    /* camel case to ... */

    public function testCamelToPascalCase(): void
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testCamelToSnakeCase(): void
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testCamelToSpinalCase(): void
    {
        $this->initRunner(__FUNCTION__);
    }

    /* pascal case to ... */

    public function testPascalToCamelCase(): void
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testPascalToSnakeCase(): void
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testPascalToSpinalCase(): void
    {
        $this->initRunner(__FUNCTION__);
    }

    /* snake case to ... */

    public function testSnakeToCamelCase(): void
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testSnakeToPascalCase(): void
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testSnakeToSpinalCase(): void
    {
        $this->initRunner(__FUNCTION__);
    }

    /* spinal case to ... */

    public function testSpinalToCamelCase(): void
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testSpinalToPascalCase(): void
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testSpinalToSnakeCase(): void
    {
        $this->initRunner(__FUNCTION__);
    }

    /* phone numbers */

    public function testToPhoneNumber(): void
    {
        $this->initRunner(__FUNCTION__);
    }

    public function testToPhoneFormat(): void
    {
        $this->initRunner(__FUNCTION__);
    }

    /**
     * @param Package  $package
     * @param int      $iteration
     * @param mixed    $provided
     * @param array    $expected
     * @param array    $arguments
     * @param string   $method
     * @param callable $callable
     */
    protected function runnerAssertCustom(Package $package, int $iteration, $provided, $expected, array $arguments, string $method, callable $callable): void
    {
        $this->runnerAssertStringTransform($provided);
    }

    /**
     * @param mixed $provided
     */
    protected function runnerAssertStringTransform($provided): void
    {
        $instance = $this->getTargetInstance();
        $instance->set($provided);
        $this->assertTrue($instance->isSame($provided));

        $instanceTwo = $instance->copy();
        $instanceTwo
            ->setMutable(true)
            ->toUpper()
            ->spacesToDashes()
            ->toLower();

        $instanceThree = $instance->spacesToDashes()->toLower();

        $this->assertSame($instanceTwo->get(), $instanceThree->get());
        $this->assertTrue($instanceTwo->isMutable());

        $instanceTwo->setMutable(false);

        $this->assertFalse($instanceTwo->isMutable());
        $this->assertCount(mb_strlen($instance->get()), $instance->split());
    }
}
