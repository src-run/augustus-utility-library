<?php

/*
 * This file is part of the `augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 * (c) Scribe Inc      <scr@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Utility;

/**
 * Class StringInspect.
 */
final class StringInspect
{
    /**
     * @param string $string
     * @param string $needle
     *
     * @return null|int
     */
    final public static function searchPositionFromLeft($string, $needle)
    {
        return self::searchPosition($string, $needle, false);
    }

    /**
     * @param string $string
     * @param string $needle
     *
     * @return null|int
     */
    final public static function searchPositionFromRight($string, $needle)
    {
        return self::searchPosition($string, $needle, true);
    }

    /**
     * @param string $string
     * @param string $needle
     * @param bool   $fromRight
     *
     * @return null|int
     */
    final public static function searchPosition($string, $needle, $fromRight = false)
    {
        $_ = self::searchPositionFunctionName($fromRight);

        if (false === ($position = $_($string, $needle))) {
            return null;
        }

        return (int) $position;
    }

    /**
     * @param bool $fromRight
     *
     * @return string
     */
    final private static function searchPositionFunctionName($fromRight)
    {
        return $fromRight ? 'mb_strrpos' : 'mb_strpos';
    }

    /**
     * @param string $string
     * @param string $search
     *
     * @return bool
     */
    final public static function contains($string, $search)
    {
        return false !== strpos($string, $search);
    }
}

/* EOF */
