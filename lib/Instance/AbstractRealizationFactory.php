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

class AbstractRealizationFactory implements RealizationFactoryInterface
{
    private $what;

    /**
     * @param object|string|null $what
     *
     * @return bool
     */
    public static function isInstantiable($what = null)
    {
        $what = static::getReflectionInstance($what);

        return !static::hasInternalAncestors($what) && !$what->isAbstract();
    }

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
     * Returns a reflection object instance for the given string or object.
     *
     * @param string|object $for
     *
     * @return \ReflectionClass
     */
    protected static function getReflectionInstance($for)
    {
        return ClassInfo::getReflection(static::getQualifiedClassName($for));
    }

    /**
     * @param \ReflectionClass $reflectionClass
     *
     * @return bool
     */
    protected static function hasInternalAncestors(\ReflectionClass $reflect)
    {
        do {
            if ($reflect->isInternal()) {
                return true;
            }
        } while ($reflect = $reflect->getParentClass());

        return false;
    }
}

/* EOF */
