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

final class EngineQuery
{
    /**
     * @param string[] ...$extensions
     */
    public static function extensionLoaded(string ...$extensions): bool
    {
        if (0 === count($extensions)) {
            throw new \InvalidArgumentException('No extensions provided for loaded check');
        }

        foreach ($extensions as $extension) {
            if (false === extension_loaded($extension)) {
                return false;
            }
        }

        return true;
    }
}
