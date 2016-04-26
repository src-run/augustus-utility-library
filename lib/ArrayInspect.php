<?php

/*
 * This file is part of the `src-run/wonka-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 * (c) Scribe Inc      <scr@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Utility;

/**
 * Class ArrayInspect
 */
class ArrayInspect
{
    /**
     * @param array $array
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
