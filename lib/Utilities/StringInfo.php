<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Utilities;

final class StringInfo
{
    /**
     * @param string $string
     * @param string $needle
     *
     * @return null|int
     */
    public static function searchPositionFromLeft(string $string, string $needle): ?int
    {
        return self::searchPosition($string, $needle, false);
    }

    /**
     * @param string $string
     * @param string $needle
     *
     * @return null|int
     */
    public static function searchPositionFromRight(string $string, string $needle): ?int
    {
        return self::searchPosition($string, $needle, true);
    }

    /**
     * @param string $string
     * @param string $search
     *
     * @return bool
     */
    public static function contains(string $string, string $search): bool
    {
        return false !== mb_strpos($string, $search);
    }

    /**
     * @param string $string
     * @param string $needle
     * @param bool   $fromRight
     *
     * @return null|int
     */
    private static function searchPosition(string $string, string $needle, bool $fromRight = false): ?int
    {
        $_ = $fromRight ? 'mb_strrpos' : 'mb_strpos';

        if (false === ($position = $_($string, $needle))) {
            return null;
        }

        return (int) $position;
    }
}
