<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Transform;

final class TransformerFactory
{
    /**
     * @param string $string
     *
     * @return StringTransform
     */
    final public static function fromString(string $string) : StringTransform
    {
        return new StringTransform($string);
    }

    /**
     * @param int $integer
     *
     * @return NumberTransform
     */
    final public static function fromInteger(int $integer) : NumberTransform
    {
        return new NumberTransform($integer);
    }

    /**
     * @param float $float
     *
     * @return NumberTransform
     */
    final public static function fromFloat(float $float) : NumberTransform
    {
        return new NumberTransform($float);
    }
}

/* EOF */
