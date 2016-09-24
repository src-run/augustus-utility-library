<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Util\Test\Instance;

use SR\Util\Info\ClassInfo;
use SR\Util\Instance\StatefulRealizationFactory;

/**
 * Class StatefulRealizationFactoryTest.
 */
class StatefulRealizationFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiateFromObject()
    {
        $obj = new ClassInfo();
        $one = StatefulRealizationFactory::instantiate($obj);

        $this->assertInstanceOf('SR\Util\Info\ClassInfo', $one);

        $two = StatefulRealizationFactory::instantiate($one);
        $this->assertInstanceOf('SR\Util\Info\ClassInfo', $two);

        $this->assertNotSame($obj, $one);
        $this->assertNotSame($one, $two);
        $this->assertNotSame($obj, $two);
    }

    public function testInstantiateFromClassName()
    {
        $obj = 'SR\Util\Info\ClassInfo';
        $one = StatefulRealizationFactory::instantiate($obj);

        $this->assertInstanceOf($obj, $one);

        $two = StatefulRealizationFactory::instantiate($one);

        $this->assertInstanceOf($obj, $two);
        $this->assertNotSame($one, $two);
    }

    public function testInstantiateWithInternal()
    {
        $this->expectException('\InvalidArgumentException');

        StatefulRealizationFactory::instantiate('SR\Util\Test\Instance\ClassInternal');
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
