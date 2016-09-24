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
 * Class EngineInspect.
 */
final class EngineInfo
{
    /**
     * @param string ...$extensions
     *
     * @return bool
     */
    final public static function extensionLoaded(...$extensions)
    {
        if (count($extensions) === 0) {
            throw new \InvalidArgumentException('No extensions provided for loaded check');
        }

        $loaded = array_filter($extensions, function ($extension) {
            return extension_loaded($extension);
        });

        return $loaded === $extensions;
    }
}

/* EOF */
