<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Util\Instance;

interface RealizationFactoryInterface
{
    /**
     * @param string|object $what
     * @param mixed         ...$constructorArguments
     *
     * @return object
     */
    public static function instantiate($what, ...$constructorArguments);

    /**
     * @param \ReflectionClass $reflectionClass
     *
     * @return bool
     */
    public static function hasInternalAncestors(\ReflectionClass $reflect);
}

/* EOF */
