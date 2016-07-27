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
use SR\Utility\ClassInstantiator;

/**
 * Class ClassInstantiator.
 */
class ClassInstantiatorTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiateFromObject()
    {
        $obj = new ClassInspect();
        $one = ClassInstantiator::instantiate($obj);

        $this->assertInstanceOf('SR\Utility\ClassInspect', $one);

        $two = ClassInstantiator::instantiate($one);
        $this->assertInstanceOf('SR\Utility\ClassInspect', $two);

        $this->assertNotSame($obj, $one);
        $this->assertNotSame($one, $two);
        $this->assertNotSame($obj, $two);
    }

    public function testInstantiateFromClassName()
    {
        $obj = 'SR\Utility\ClassInspect';
        $one = ClassInstantiator::instantiate($obj);

        $this->assertInstanceOf($obj, $one);

        $two = ClassInstantiator::instantiate($one);

        $this->assertInstanceOf($obj, $two);
        $this->assertNotSame($one, $two);
    }

    public function testInstantiateWithInternal()
    {
        $this->expectException('\InvalidArgumentException');

        ClassInstantiator::instantiate('SR\Utility\Tests\ClassInternal');
    }
}

/**
 * Fixtures
 */

class ClassInternal extends ClassInternalParent {}
class ClassInternalParent extends \SplFileInfo
{
    public function __construct()
    {
        parent::__construct(__FILE__);
    }
}

/* EOF */
