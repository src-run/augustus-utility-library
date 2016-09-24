<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Test\Transformer;

class NumberTransformTest
{
    /**
     * @return array[]
     */
    public function providerToInterger()
    {
        return [
            [1, 1.25],
            [10, 10.12345],
        ];
    }

    /**
     * @dataProvider providerToInteger
     */
    public function testToInteger($expected, $provided)
    {
        var_dump([
            $expected,
            $provided,
        ]);
    }
}
