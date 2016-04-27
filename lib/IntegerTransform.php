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
 * Class IntegerTransform.
 */
class IntegerTransform
{
    /**
     * Convert an integer from one base to another with optional prevision.
     *
     * @param int      $integer
     * @param int      $base
     * @param int      $toBase
     * @param int|null $precision
     * @param bool     $baseIsMaxInteger
     *
     * @throws \InvalidArgumentException
     *
     * @return int
     */
    final public static function toBase($integer, $base, $toBase, $precision = null, $baseIsMaxInteger = false)
    {
        if (0 === $base) {
            throw new \InvalidArgumentException('Cannot convert from a base of zero.');
        }

        $converted = $integer * $toBase / $base;

        if (null !== $precision) {
            $converted = round($converted, $precision);
        }

        if ($precision === 0) {
            $converted = (int) $converted;
        }

        if ($baseIsMaxInteger && $converted > $toBase) {
            return $toBase;
        }

        return $converted;
    }
}

/* EOF */
