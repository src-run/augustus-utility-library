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
 * Class ClassUtil.
 */
class ClassUtil
{
    /**
     * @param string|object $for
     *
     * @return string
     */
    final public static function getName($for)
    {
        return (string) self::newClassReflection($for)->getName();
    }

    /**
     * @param string|object $for
     *
     * @return string
     */
    final public static function getNameShort($for)
    {
        return (string) self::newClassReflection($for)->getShortName();
    }

    /**
     * @param string|object $for
     *
     * @return string
     */
    final public static function getNamespace($for)
    {
        return self::newClassReflection($for)->getNamespaceName();
    }

    /**
     * @param string|object $for
     *
     * @return string[]
     */
    final public static function getNamespaceArray($for)
    {
        return (array) explode('\\', self::getNamespace($for));
    }

    /**
     * @param mixed $class
     *
     * @return bool
     */
    final public static function isClass($class)
    {
        try {
            return static::assertClass($class);
        } catch (\InvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * @param mixed $instance
     *
     * @return bool
     */
    final public static function isInstance($instance)
    {
        try {
            return static::assertInstance($instance);
        } catch (\InvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * @param string $trait
     *
     * @return bool
     */
    final public static function isTrait($trait)
    {
        try {
            return static::assertTrait($trait);
        } catch (\InvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * @param mixed $class
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    final public static function assertClass($class)
    {
        if (is_string($class) && class_exists((string) $class)) {
            return true;
        }

        throw new \InvalidArgumentException('Failed asserting passed value is valid class name.');
    }

    /**
     * @param mixed $instance
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    final public static function assertInstance($instance)
    {
        if (is_object($instance)) {
            return true;
        }

        throw new \InvalidArgumentException('Failed asserting passed value is an object instance.');
    }

    /**
     * @param mixed $trait
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    final public static function assertTrait($trait)
    {
        if (is_string($trait) && trait_exists((string) $trait)) {
            return true;
        }

        throw new \InvalidArgumentException('Failed asserting passed value is valid trait name.');
    }

    /**
     * @param string|object $for
     *
     * @return null|\ReflectionClass|\ReflectionObject
     */
    final public static function newClassReflection($for)
    {
        if (self::isClass($for)) {
            return new \ReflectionClass($for);
        }

        if (self::isInstance($for)) {
            return new \ReflectionObject($for);
        }

        return null;
    }
}

/* EOF */
