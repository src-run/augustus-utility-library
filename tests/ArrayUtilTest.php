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

use SR\Utility\ArrayUtil;

/**
 * Class ArrayUtilTest.
 */
class ArrayUtilTest extends \PHPUnit_Framework_TestCase
{
    public function testIsHash()
    {
        $isNotHash = [
            'one', 'two', 'three'
        ];

        $isHash = [
            'o' => 'one', 't' => 'two', 'h' => 'three'
        ];

        $this->assertTrue(ArrayUtil::isHash($isHash));
        $this->assertFalse(ArrayUtil::isHash($isNotHash));
        $this->assertNull(ArrayUtil::isHash([]));
    }
}

/* EOF */
