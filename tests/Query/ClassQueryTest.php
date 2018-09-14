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

use PHPUnit\Framework\TestCase;
use SR\Tests\Utilities\Fixture\FixtureInterface;
use SR\Tests\Utilities\Fixture\FixtureTrait;
use SR\Tests\Utilities\Fixture\IsInstanceOfThrowableFixture;
use SR\Tests\Utilities\Fixture\NotInstanceOfThrowableFixture;
use SR\Tests\Utilities\Resources\Fixtures\ClassFixture;
use SR\Utilities\Query\ClassQuery;

/**
 * @covers \SR\Utilities\Query\ClassQuery
 */
class ClassQueryTest extends TestCase
{
    public function testGetInformation()
    {
        $instance = new \SR\Utilities\Query\ClassQuery();

        static::assertSame(__CLASS__, \SR\Utilities\Query\ClassQuery::getName(__CLASS__));
        static::assertSame(get_class($instance), \SR\Utilities\Query\ClassQuery::getName($instance));

        static::assertSame('ClassQueryTest', \SR\Utilities\Query\ClassQuery::getNameShort(__CLASS__));
        static::assertSame('ClassQuery', \SR\Utilities\Query\ClassQuery::getNameShort($instance));

        static::assertSame(__NAMESPACE__, \SR\Utilities\Query\ClassQuery::getNamespace(__CLASS__));
        static::assertSame('SR\Utilities\Query', \SR\Utilities\Query\ClassQuery::getNamespace($instance));

        static::assertSame(explode('\\', __NAMESPACE__), \SR\Utilities\Query\ClassQuery::getNamespaceArray(__CLASS__));
        static::assertSame(explode('\\', 'SR\Utilities\Query'), \SR\Utilities\Query\ClassQuery::getNamespaceArray($instance));
    }

    public function testClassTester()
    {
        $instance = new \SR\Utilities\Query\ClassQuery();

        static::assertTrue(\SR\Utilities\Query\ClassQuery::assertClass(__CLASS__));
        static::assertTrue(\SR\Utilities\Query\ClassQuery::isClass(__CLASS__));
        static::assertFalse(\SR\Utilities\Query\ClassQuery::isClass($instance));

        $this->expectException(\InvalidArgumentException::class);
        \SR\Utilities\Query\ClassQuery::assertClass($instance);
    }

    public function testInstanceTester()
    {
        $instance = new \SR\Utilities\Query\ClassQuery();

        static::assertTrue(\SR\Utilities\Query\ClassQuery::assertInstance($instance));
        static::assertTrue(\SR\Utilities\Query\ClassQuery::isInstance($instance));
        static::assertFalse(\SR\Utilities\Query\ClassQuery::isInstance(__CLASS__));

        $this->expectException(\InvalidArgumentException::class);
        \SR\Utilities\Query\ClassQuery::assertInstance(__CLASS__);
    }

    public function testInterfaceTester()
    {
        $interface = FixtureInterface::class;

        static::assertTrue(ClassQuery::assertInterface($interface));
        static::assertTrue(\SR\Utilities\Query\ClassQuery::isInterface($interface));
        static::assertFalse(ClassQuery::isInterface(__CLASS__));

        $this->expectException(\InvalidArgumentException::class);
        ClassQuery::assertInterface(__CLASS__);
    }

    public function testTraitTester()
    {
        $trait = FixtureTrait::class;

        static::assertTrue(ClassQuery::assertTrait($trait));
        static::assertTrue(\SR\Utilities\Query\ClassQuery::isTrait($trait));
        static::assertFalse(\SR\Utilities\Query\ClassQuery::isTrait(__CLASS__));

        $this->expectException(\InvalidArgumentException::class);
        ClassQuery::assertTrait(__CLASS__);
    }

    public function testNewClassReflection()
    {
        $instanceReflection = \SR\Utilities\Query\ClassQuery::getReflection(new \SR\Utilities\Query\ClassQuery());
        $this->assertTrue($instanceReflection instanceof \ReflectionObject);

        $classReflection = ClassQuery::getReflection(__CLASS__);
        $this->assertTrue($classReflection instanceof \ReflectionClass);

        $this->expectException(\InvalidArgumentException::class);
        \SR\Utilities\Query\ClassQuery::getReflection('Invalud\Path\To\A\Namespaced\Class\Id\Really\Hope');
    }

    public function testThrowableEquitable()
    {
        $class = IsInstanceOfThrowableFixture::class;
        $instance = new $class();
        $this->assertTrue(ClassQuery::isThrowableEquitable($class));
        $this->assertTrue(\SR\Utilities\Query\ClassQuery::isThrowableEquitable($instance));

        $class = NotInstanceOfThrowableFixture::class;
        $instance = new $class();
        $this->assertFalse(ClassQuery::isThrowableEquitable($class));
        $this->assertFalse(\SR\Utilities\Query\ClassQuery::isThrowableEquitable($instance));

        $this->assertFalse(
            \SR\Utilities\Query\ClassQuery::isThrowableEquitable(__NAMESPACE__.'\This\Class\Does\Not\Exist'));
    }

    public function testNonAccessibleMethodAndPropertyAccess()
    {
        $classFixture = new ClassFixture();

        $protectedProperty = \SR\Utilities\Query\ClassQuery::getNonAccessiblePropertyReflection('protectedProperty', $classFixture);
        $privateProperty = \SR\Utilities\Query\ClassQuery::getNonAccessiblePropertyReflection('privateProperty', $classFixture);
        $protectedMethod = \SR\Utilities\Query\ClassQuery::getNonAccessibleMethodReflection('getProtectedProperty', $classFixture);
        $privateMethod = \SR\Utilities\Query\ClassQuery::getNonAccessibleMethodReflection('getPrivateProperty', $classFixture);

        $this->assertInstanceOf(\ReflectionProperty::class, $protectedProperty);
        $this->assertInstanceOf(\ReflectionProperty::class, $privateProperty);
        $this->assertInstanceOf(\ReflectionMethod::class, $protectedMethod);
        $this->assertInstanceOf(\ReflectionMethod::class, $privateMethod);

        $this->assertSame(
            $protectedProperty->getValue($classFixture),
            $protectedPropVal = \SR\Utilities\Query\ClassQuery::getNonAccessiblePropertyValue('protectedProperty', $classFixture)
        );
        $this->assertSame(
            $privateProperty->getValue($classFixture),
            $privatePropVal = \SR\Utilities\Query\ClassQuery::getNonAccessiblePropertyValue('privateProperty', $classFixture)
        );
        $this->assertSame(
            $protectedMethod->invoke($classFixture),
            \SR\Utilities\Query\ClassQuery::getNonAccessibleMethodInvokeReturn('getProtectedProperty', $classFixture)
        );
        $this->assertSame(
            $privateMethod->invoke($classFixture),
            \SR\Utilities\Query\ClassQuery::getNonAccessibleMethodInvokeReturn('getPrivateProperty', $classFixture)
        );

        \SR\Utilities\Query\ClassQuery::setNonAccessiblePropertyValue('protectedProperty', $classFixture, 'foo');
        \SR\Utilities\Query\ClassQuery::setNonAccessiblePropertyValue('privateProperty', $classFixture, 'bar');

        $this->assertNotSame(
            $protectedPropVal,
            \SR\Utilities\Query\ClassQuery::getNonAccessiblePropertyValue('protectedProperty', $classFixture)
        );
        $this->assertSame(
            'foo',
            \SR\Utilities\Query\ClassQuery::getNonAccessiblePropertyValue('protectedProperty', $classFixture)
        );
        $this->assertNotSame(
            $privatePropVal,
            \SR\Utilities\Query\ClassQuery::getNonAccessiblePropertyValue('privateProperty', $classFixture)
        );
        $this->assertSame(
            'bar',
            \SR\Utilities\Query\ClassQuery::getNonAccessiblePropertyValue('privateProperty', $classFixture)
        );
    }

    /**
     * @group legacy
     * @group bcl
     */
    public function testDeprecatedNamespace(): void
    {
        $this->assertInstanceOf(ClassQuery::class, new \SR\Util\Info\ClassInfo());
        $this->assertInstanceOf(ClassQuery::class, new \SR\Utilities\ClassQuery());
    }
}
