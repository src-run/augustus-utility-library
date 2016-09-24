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

use SR\Info\ClassInfo;

class ClassInfoTest extends \PHPUnit_Framework_TestCase
{
    public function testGetInformation()
    {
        $instance = new ClassInfo();

        static::assertSame(__CLASS__, ClassInfo::getName(__CLASS__));
        static::assertSame(get_class($instance), ClassInfo::getName($instance));

        static::assertSame('ClassInfoTest', ClassInfo::getNameShort(__CLASS__));
        static::assertSame('ClassInfo', ClassInfo::getNameShort($instance));

        static::assertSame(__NAMESPACE__, ClassInfo::getNamespace(__CLASS__));
        static::assertSame('SR\Info', ClassInfo::getNamespace($instance));

        static::assertSame(explode('\\', __NAMESPACE__), ClassInfo::getNamespaceArray(__CLASS__));
        static::assertSame(explode('\\', 'SR\Info'), ClassInfo::getNamespaceArray($instance));
    }

    public function testClassTester()
    {
        $instance = new ClassInfo();

        static::assertTrue(ClassInfo::assertClass(__CLASS__));
        static::assertTrue(ClassInfo::isClass(__CLASS__));
        static::assertFalse(ClassInfo::isClass($instance));

        $this->expectException('\InvalidArgumentException');
        ClassInfo::assertClass($instance);
    }

    public function testInstanceTester()
    {
        $instance = new ClassInfo();

        static::assertTrue(ClassInfo::assertInstance($instance));
        static::assertTrue(ClassInfo::isInstance($instance));
        static::assertFalse(ClassInfo::isInstance(__CLASS__));

        $this->expectException('\InvalidArgumentException');
        ClassInfo::assertInstance(__CLASS__);
    }

    public function testInterfaceTester()
    {
        $interface = 'SR\Test\Fixture\FixtureInterface';

        static::assertTrue(ClassInfo::assertInterface($interface));
        static::assertTrue(ClassInfo::isInterface($interface));
        static::assertFalse(ClassInfo::isInterface(__CLASS__));

        $this->expectException('\InvalidArgumentException');
        ClassInfo::assertInterface(__CLASS__);
    }

    public function testTraitTester()
    {
        $trait = 'SR\Test\Fixture\FixtureTrait';

        static::assertTrue(ClassInfo::assertTrait($trait));
        static::assertTrue(ClassInfo::isTrait($trait));
        static::assertFalse(ClassInfo::isTrait(__CLASS__));

        $this->expectException('\InvalidArgumentException');
        ClassInfo::assertTrait(__CLASS__);
    }

    public function testNewClassReflection()
    {
        $instanceReflection = ClassInfo::getReflection(new ClassInfo());
        $this->assertTrue($instanceReflection instanceof \ReflectionObject);

        $classReflection = ClassInfo::getReflection(__CLASS__);
        $this->assertTrue($classReflection instanceof \ReflectionClass);

        $invalidReflection = ClassInfo::getReflection('Invalud\Path\To\A\Namespaced\Class\Id\Really\Hope');
        $this->assertNull($invalidReflection);
    }

    public function testThrowableEquitable()
    {
        $class = 'SR\Test\Fixture\IsInstanceOfThrowableFixture';
        $instance = new $class();
        $this->assertTrue(ClassInfo::isThrowableEquitable($class));
        $this->assertTrue(ClassInfo::isThrowableEquitable($instance));

        $class = 'SR\Test\Fixture\NotInstanceOfThrowableFixture';
        $instance = new $class();
        $this->assertFalse(ClassInfo::isThrowableEquitable($class));
        $this->assertFalse(ClassInfo::isThrowableEquitable($instance));

        $this->assertFalse(ClassInfo::isThrowableEquitable(__NAMESPACE__.'\This\Class\Does\Not\Exist'));
    }
}
