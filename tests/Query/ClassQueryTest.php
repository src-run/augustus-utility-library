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
    public function testIsReflectable()
    {
        $this->assertFalse(ClassQuery::isReflectable('foobar'));
        $this->assertTrue(ClassQuery::isReflectable(new \stdClass()));
    }

    public function testTryReflection()
    {
        $this->assertInstanceOf(\ReflectionObject::class, ClassQuery::tryReflection(new \stdClass()));
        $this->assertNull(ClassQuery::tryReflection('foobar'));
    }

    public function testSlashInputNoInterpreterError()
    {
        $this->assertFalse(ClassQuery::isInstance('\\'));
        $this->assertFalse(ClassQuery::isClass('\\'));
        $this->assertFalse(ClassQuery::isTrait('\\'));
        $this->assertFalse(ClassQuery::isInterface('\\'));
    }

    public function testGetInformation()
    {
        $instance = new ClassQuery();

        static::assertSame(__CLASS__, ClassQuery::getName(__CLASS__));
        static::assertSame(get_class($instance), ClassQuery::getName($instance));

        static::assertSame('ClassQueryTest', ClassQuery::getNameShort(__CLASS__));
        static::assertSame('ClassQuery', ClassQuery::getNameShort($instance));

        static::assertSame(__NAMESPACE__, ClassQuery::getNamespace(__CLASS__));
        static::assertSame('SR\Utilities\Query', ClassQuery::getNamespace($instance));

        static::assertSame(explode('\\', __NAMESPACE__), ClassQuery::getNamespaceArray(__CLASS__));
        static::assertSame(explode('\\', 'SR\Utilities\Query'), ClassQuery::getNamespaceArray($instance));
    }

    public function testClassTester()
    {
        $instance = new ClassQuery();

        static::assertTrue(ClassQuery::assertClass(__CLASS__));
        static::assertTrue(ClassQuery::isClass(__CLASS__));
        static::assertFalse(ClassQuery::isClass($instance));

        $this->expectException(\InvalidArgumentException::class);
        ClassQuery::assertClass($instance);
    }

    public function testInstanceTester()
    {
        $instance = new ClassQuery();

        static::assertTrue(ClassQuery::assertInstance($instance));
        static::assertTrue(ClassQuery::isInstance($instance));
        static::assertFalse(ClassQuery::isInstance(__CLASS__));

        $this->expectException(\InvalidArgumentException::class);
        ClassQuery::assertInstance(__CLASS__);
    }

    public function testInterfaceTester()
    {
        $interface = FixtureInterface::class;

        static::assertTrue(ClassQuery::assertInterface($interface));
        static::assertTrue(ClassQuery::isInterface($interface));
        static::assertFalse(ClassQuery::isInterface(__CLASS__));

        $this->expectException(\InvalidArgumentException::class);
        ClassQuery::assertInterface(__CLASS__);
    }

    public function testTraitTester()
    {
        $trait = FixtureTrait::class;

        static::assertTrue(ClassQuery::assertTrait($trait));
        static::assertTrue(ClassQuery::isTrait($trait));
        static::assertFalse(ClassQuery::isTrait(__CLASS__));

        $this->expectException(\InvalidArgumentException::class);
        ClassQuery::assertTrait(__CLASS__);
    }

    public function testNewClassReflection()
    {
        $instanceReflection = ClassQuery::getReflection(new ClassQuery());
        $this->assertInstanceOf(\ReflectionObject::class, $instanceReflection);

        $classReflection = ClassQuery::getReflection(__CLASS__);
        $this->assertInstanceOf(\ReflectionClass::class, $classReflection);

        $this->expectException(\InvalidArgumentException::class);
        ClassQuery::getReflection('Invalud\Path\To\A\Namespaced\Class\Id\Really\Hope');
    }

    public function testThrowableEquitable()
    {
        $class = IsInstanceOfThrowableFixture::class;
        $instance = new $class();
        $this->assertTrue(ClassQuery::isThrowableEquitable($class));
        $this->assertTrue(ClassQuery::isThrowableEquitable($instance));

        $class = NotInstanceOfThrowableFixture::class;
        $instance = new $class();
        $this->assertFalse(ClassQuery::isThrowableEquitable($class));
        $this->assertFalse(ClassQuery::isThrowableEquitable($instance));

        $this->assertFalse(
            ClassQuery::isThrowableEquitable(__NAMESPACE__ . '\This\Class\Does\Not\Exist'));
    }

    public function testNonAccessibleMethodAndPropertyAccess()
    {
        $classFixture = new ClassFixture();

        $protectedProperty = ClassQuery::getNonAccessiblePropertyReflection('protectedProperty', $classFixture);
        $privateProperty = ClassQuery::getNonAccessiblePropertyReflection('privateProperty', $classFixture);
        $protectedMethod = ClassQuery::getNonAccessibleMethodReflection('getProtectedProperty', $classFixture);
        $privateMethod = ClassQuery::getNonAccessibleMethodReflection('getPrivateProperty', $classFixture);

        $this->assertInstanceOf(\ReflectionProperty::class, $protectedProperty);
        $this->assertInstanceOf(\ReflectionProperty::class, $privateProperty);
        $this->assertInstanceOf(\ReflectionMethod::class, $protectedMethod);
        $this->assertInstanceOf(\ReflectionMethod::class, $privateMethod);

        $this->assertSame(
            $protectedProperty->getValue($classFixture),
            $protectedPropVal = ClassQuery::getNonAccessiblePropertyValue('protectedProperty', $classFixture)
        );
        $this->assertSame(
            $privateProperty->getValue($classFixture),
            $privatePropVal = ClassQuery::getNonAccessiblePropertyValue('privateProperty', $classFixture)
        );
        $this->assertSame(
            $protectedMethod->invoke($classFixture),
            ClassQuery::getNonAccessibleMethodInvokeReturn('getProtectedProperty', $classFixture)
        );
        $this->assertSame(
            $privateMethod->invoke($classFixture),
            ClassQuery::getNonAccessibleMethodInvokeReturn('getPrivateProperty', $classFixture)
        );

        ClassQuery::setNonAccessiblePropertyValue('protectedProperty', $classFixture, 'foo');
        ClassQuery::setNonAccessiblePropertyValue('privateProperty', $classFixture, 'bar');

        $this->assertNotSame(
            $protectedPropVal,
            ClassQuery::getNonAccessiblePropertyValue('protectedProperty', $classFixture)
        );
        $this->assertSame(
            'foo',
            ClassQuery::getNonAccessiblePropertyValue('protectedProperty', $classFixture)
        );
        $this->assertNotSame(
            $privatePropVal,
            ClassQuery::getNonAccessiblePropertyValue('privateProperty', $classFixture)
        );
        $this->assertSame(
            'bar',
            ClassQuery::getNonAccessiblePropertyValue('privateProperty', $classFixture)
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
