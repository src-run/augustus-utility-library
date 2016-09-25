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
use SR\Util\Instance\StatelessRealizationFactory;

/**
 * Class StatelessRealizationFactoryTest.
 */
class StatelessRealizationFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiateFromObject()
    {
        $obj = new ClassInfo();
        $one = StatelessRealizationFactory::instantiate($obj);

        $this->assertInstanceOf('SR\Util\Info\ClassInfo', $one);

        $two = StatelessRealizationFactory::instantiate($one);
        $this->assertInstanceOf('SR\Util\Info\ClassInfo', $two);

        $this->assertNotSame($obj, $one);
        $this->assertNotSame($one, $two);
        $this->assertNotSame($obj, $two);
    }

    public function testInstantiateFromClassName()
    {
        $obj = 'SR\Util\Info\ClassInfo';
        $one = StatelessRealizationFactory::instantiate($obj);

        $this->assertInstanceOf($obj, $one);

        $two = StatelessRealizationFactory::instantiate($one);

        $this->assertInstanceOf($obj, $two);
        $this->assertNotSame($one, $two);
    }

    public function testInstantiateWithInternal()
    {
        $this->expectException('\InvalidArgumentException');

        StatelessRealizationFactory::instantiate('SR\Util\Test\Instance\StatelessClassInternal');
    }
}

/**
 * Fixture class for test.
 */
class StatelessClassInternal extends StatelessClassInternalParent
{
}

/**
 * Fixture class for test.
 */
class StatelessClassInternalParent extends \SplFileInfo
{
    public function __construct()
    {
        parent::__construct(__FILE__);
    }
}

/* EOF */
