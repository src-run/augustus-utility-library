<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Util\Info;

/**
 * Class ArrayInspect.
 */
final class ArrayInfo
{
    /**
     * @param mixed[] $array
     *
     * @return bool|null
     */
    final public static function isAssociative(array $array)
    {
        if (count($array) === 0) {
            return null;
        }

        $keys = array_keys($array);

        return array_keys($keys) !== $keys;
    }
}

/* EOF */
