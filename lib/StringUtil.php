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
 * Class StringUtil.
 */
class StringUtil
{
    /**
     * @param string $string
     * @param string $needle
     *
     * @return int|null
     */
    final static public function searchPositionFromLeft($string, $needle)
    {
        return self::searchPosition($string, $needle, false);
    }

    /**
     * @param string $string
     * @param string $needle
     *
     * @return int|null
     */
    final static public function searchPositionFromRight($string, $needle)
    {
        return self::searchPosition($string, $needle, true);
    }

    /**
     * @param string $string
     * @param string $needle
     * @param bool   $fromRight
     *
     * @return int|null
     */
    final static public function searchPosition($string, $needle, $fromRight = false)
    {
        $_ = self::searchPositionFunctionSelect($fromRight);

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
    final static private function searchPositionFunctionSelect($fromRight)
    {
        return $fromRight ? 'mb_strrpos' : 'mb_strpos';
    }
}

/* EOF */
