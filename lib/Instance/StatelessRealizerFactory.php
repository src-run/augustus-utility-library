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

class StatelessRealizerFactory
{
    /**
     * @var object|string
     */
    private $what;

    /**
     * @var mixed[]
     */
    private $with;

    /**
     * Provide the item to instantiate and any additonal arguments during construction.
     *
     * @param object|string $what
     * @param mixed[]       ...$with
     */
    public function __construct($what, ...$with)
    {
        $this->conceiveItem = $what;
        $this->conceiveWith = $with;
    }

    public function isSupported()
    {
        return $this->
    }

    /**
     * @param string|object $what
     * @param mixed         ...$constructorArguments
     *
     * @return object
     */
    final public static function Incepter($what, ...$constructorArguments)
    {
        $classFqn = static::getQualifiedClassName($what);

        if (isset(static::$cachedInstances[$classFqn])) {
            return clone static::$cachedInstances[$classFqn];
        }

        return static::buildAndCache($classFqn, ...$constructorArguments);
    }

    /**
     * @param string|object $what
     *
     * @return string
     */
    final private static function getQualifiedClassName($what)
    {
        if (ClassInfo::isClass($what)) {
            return $what;
        }

        return ClassInfo::getNameQualified($what);
    }

    /**
     * @param string $classFqn
     * @param mixed[] ...$constructorArguments
     *
     * @return object
     */
    final private static function buildAndCache($classFqn, ...$constructorArguments)
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

    /**
     * @param \ReflectionClass $reflectionClass
     *
     * @return bool
     */
    final private static function isInstantiable(\ReflectionClass $reflectionClass)
    {
        return !static::hasInternalAncestors($reflectionClass) && !$reflectionClass->isAbstract();
    }

    /**
     * @param \ReflectionClass $reflectionClass
     *
     * @return bool
     */
    final public static function hasInternalAncestors(\ReflectionClass $reflectionClass)
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
