<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Test\Instantiation;

use SR\Info\ClassInfo;
use SR\Util\Instantiators\Instantiator;

/**
 * Class InstantiatorTest.
 */
class InstantiatorTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiateFromObject()
    {
        $obj = new ClassInfo();
        $one = Instantiator::instantiate($obj);

        $this->assertInstanceOf('SR\Info\ClassInspect', $one);

        $two = Instantiator::instantiate($one);
        $this->assertInstanceOf('SR\Info\ClassInspect', $two);

        $this->assertNotSame($obj, $one);
        $this->assertNotSame($one, $two);
        $this->assertNotSame($obj, $two);
    }

    public function testInstantiateFromClassName()
    {
        $obj = 'SR\Info\ClassInspect';
        $one = Instantiator::instantiate($obj);

        $this->assertInstanceOf($obj, $one);

        $two = Instantiator::instantiate($one);

        $this->assertInstanceOf($obj, $two);
        $this->assertNotSame($one, $two);
    }

    public function testInstantiateWithInternal()
    {
        $this->expectException('\InvalidArgumentException');

        Instantiator::instantiate('SR\Test\Info\ClassInternal');
    }
}

/**
 * Fixture class for test.
 */
class ClassInternal extends ClassInternalParent
{
}

/**
 * Fixture class for test.
 */
class ClassInternalParent extends \SplFileInfo
{
    public function __construct()
    {
        parent::__construct(__FILE__);
    }
}

/* EOF */
