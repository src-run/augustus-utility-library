<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Utilities\Query;

final class StringQuery
{
    public static function searchPositionFromLeft(string $string, string $needle): ?int
    {
        return self::searchPosition($string, $needle, false);
    }

    public static function searchPositionFromRight(string $string, string $needle): ?int
    {
        return self::searchPosition($string, $needle, true);
    }

    public static function contains(string $string, string $search): bool
    {
        return false !== mb_strpos($string, $search);
    }

    private static function searchPosition(string $string, string $needle, bool $fromRight = false): ?int
    {
        $_ = $fromRight ? 'mb_strrpos' : 'mb_strpos';

        if (false === ($position = $_($string, $needle))) {
            return null;
        }

        return (int) $position;
    }
}
