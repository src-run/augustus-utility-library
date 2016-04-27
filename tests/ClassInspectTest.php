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

use SR\Utility\ClassInspect;

/**
 * Class ClassInspectTest.
 */
class ClassInspectTest extends \PHPUnit_Framework_TestCase
{
    public function testGetInformation()
    {
        $instance = new ClassInspect();

        static::assertSame(__CLASS__, ClassInspect::getName(__CLASS__));
        static::assertSame(get_class($instance), ClassInspect::getName($instance));

        static::assertSame('ClassInspectTest', ClassInspect::getNameShort(__CLASS__));
        static::assertSame('ClassInspect', ClassInspect::getNameShort($instance));

        static::assertSame(__NAMESPACE__, ClassInspect::getNamespace(__CLASS__));
        static::assertSame('SR\Utility', ClassInspect::getNamespace($instance));

        static::assertSame(explode('\\', __NAMESPACE__), ClassInspect::getNamespaceArray(__CLASS__));
        static::assertSame(explode('\\', 'SR\Utility'), ClassInspect::getNamespaceArray($instance));
    }

    public function testClassTester()
    {
        $instance = new ClassInspect();

        static::assertTrue(ClassInspect::assertClass(__CLASS__));
        static::assertTrue(ClassInspect::isClass(__CLASS__));
        static::assertFalse(ClassInspect::isClass($instance));

        $this->expectException('\InvalidArgumentException');
        ClassInspect::assertClass($instance);
    }

    public function testInstanceTester()
    {
        $instance = new ClassInspect();

        static::assertTrue(ClassInspect::assertInstance($instance));
        static::assertTrue(ClassInspect::isInstance($instance));
        static::assertFalse(ClassInspect::isInstance(__CLASS__));

        $this->expectException('\InvalidArgumentException');
        ClassInspect::assertInstance(__CLASS__);
    }

    public function testTraitTester()
    {
        $trait = 'SR\Utility\Tests\Fixture\FixtureTrait';

        static::assertTrue(ClassInspect::assertTrait($trait));
        static::assertTrue(ClassInspect::isTrait($trait));
        static::assertFalse(ClassInspect::isTrait(__CLASS__));

        $this->expectException('\InvalidArgumentException');
        ClassInspect::assertTrait(__CLASS__);
    }

    public function testNewClassReflection()
    {
        $instanceReflection = ClassInspect::getReflection(new ClassInspect());
        $this->assertTrue($instanceReflection instanceof \ReflectionObject);

        $classReflection = ClassInspect::getReflection(__CLASS__);
        $this->assertTrue($classReflection instanceof \ReflectionClass);

        $invalidReflection = ClassInspect::getReflection('Invalud\Path\To\A\Namespaced\Class\Id\Really\Hope');
        $this->assertNull($invalidReflection);
    }
}

/* EOF */
