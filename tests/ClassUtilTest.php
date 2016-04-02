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

use SR\Utility\ClassUtil;

/**
 * Class ClassUtilTest.
 */
class ClassUtilTest extends \PHPUnit_Framework_TestCase
{
    public function testGetInformation()
    {
        $instance = new ClassUtil();

        static::assertSame(__CLASS__, ClassUtil::getName(__CLASS__));
        static::assertSame(get_class($instance), ClassUtil::getName($instance));

        static::assertSame('ClassUtilTest', ClassUtil::getNameShort(__CLASS__));
        static::assertSame('ClassUtil', ClassUtil::getNameShort($instance));

        static::assertSame(__NAMESPACE__, ClassUtil::getNamespace(__CLASS__));
        static::assertSame('SR\Utility', ClassUtil::getNamespace($instance));

        static::assertSame(explode('\\', __NAMESPACE__), ClassUtil::getNamespaceArray(__CLASS__));
        static::assertSame(explode('\\', 'SR\Utility'), ClassUtil::getNamespaceArray($instance));
    }
    
    public function testClassTester()
    {
        $instance = new ClassUtil();

        static::assertTrue(ClassUtil::assertClass(__CLASS__));
        static::assertTrue(ClassUtil::isClass(__CLASS__));
        static::assertFalse(ClassUtil::isClass($instance));

        $this->expectException('\InvalidArgumentException');
        ClassUtil::assertClass($instance);
    }

    public function testInstanceTester()
    {
        $instance = new ClassUtil();

        static::assertTrue(ClassUtil::assertInstance($instance));
        static::assertTrue(ClassUtil::isInstance($instance));
        static::assertFalse(ClassUtil::isInstance(__CLASS__));

        $this->expectException('\InvalidArgumentException');
        ClassUtil::assertInstance(__CLASS__);
    }

    public function testTraitTester()
    {
        $trait = 'SR\Utility\Tests\Fixture\FixtureTrait';

        static::assertTrue(ClassUtil::assertTrait($trait));
        static::assertTrue(ClassUtil::isTrait($trait));
        static::assertFalse(ClassUtil::isTrait(__CLASS__));

        $this->expectException('\InvalidArgumentException');
        ClassUtil::assertTrait(__CLASS__);
    }

    public function testNewClassReflection()
    {
        $instanceReflection = ClassUtil::newClassReflection(new ClassUtil());
        $this->assertTrue($instanceReflection instanceof \ReflectionObject);

        $classReflection = ClassUtil::newClassReflection(__CLASS__);
        $this->assertTrue($classReflection instanceof \ReflectionClass);

        $invalidReflection = ClassUtil::newClassReflection('Invalud\Path\To\A\Namespaced\Class\Id\Really\Hope');
        $this->assertNull($invalidReflection);
    }
}

/* EOF */
