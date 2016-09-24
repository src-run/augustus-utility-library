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

use SR\Util\Info\ClassInfo;

abstract class AbstractRealizationFactory implements RealizationFactoryInterface
{
    /**
     * @param string|object $what
     *
     * @return string
     */
    protected static function getQualifiedClassName($what)
    {
        if (ClassInfo::isClass($what)) {
            return $what;
        }

        return ClassInfo::getNameQualified($what);
    }

    /**
     * @param \ReflectionClass $reflectionClass
     *
     * @return bool
     */
    protected static function isInstantiable(\ReflectionClass $reflectionClass)
    {
        return !static::hasInternalAncestors($reflectionClass) && !$reflectionClass->isAbstract();
    }

    /**
     * @param \ReflectionClass $reflectionClass
     *
     * @return bool
     */
    public static function hasInternalAncestors(\ReflectionClass $reflectionClass)
    {
        do {
            if ($reflectionClass->isInternal()) {
                return true;
            }
        } while ($reflectionClass = $reflectionClass->getParentClass());

        return false;
    }
}

/* EOF */
