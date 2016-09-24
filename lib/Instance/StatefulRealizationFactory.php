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

final class StatefulRealizationFactory extends AbstractRealizationFactory
{
    /**
     * @var object[]
     */
    private static $cachedInstances;

    /**
     * @param string|object $what
     * @param mixed         ...$constructorArguments
     *
     * @return object
     */
    final public static function instantiate($what, ...$constructorArguments)
    {
        $classFqn = static::getQualifiedClassName($what);

        if (isset(static::$cachedInstances[$classFqn])) {
            return clone static::$cachedInstances[$classFqn];
        }

        return static::buildAndCache($classFqn, ...$constructorArguments);
    }

    /**
     * @param string $classFqn
     * @param mixed[] ...$constructorArguments
     *
     * @return object
     */
    private static function buildAndCache($classFqn, ...$constructorArguments)
    {
        $reflectionClass = ClassInfo::getReflection($classFqn);

        if (!static::isInstantiable($reflectionClass)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" is not instantiable.', $classFqn));
        }

        $instance = $reflectionClass->newInstanceArgs($constructorArguments);

        if ($reflectionClass->isCloneable()) {
            static::$cachedInstances[$classFqn] = clone $instance;
        }

        return $instance;
    }
}
