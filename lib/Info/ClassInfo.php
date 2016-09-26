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
 * Class ClassInspect.
 */
final class ClassInfo
{
    /**
     * @param string|object $for
     * @param bool          $qualified
     *
     * @return string
     */
    final public static function getName($for, $qualified = true)
    {
        return $qualified ? static::getNameQualified($for) : static::getNameShort($for);
    }

    /**
     * @param string|object $for
     *
     * @return string
     */
    final public static function getNameQualified($for)
    {
        return (string) static::getReflection($for)->getName();
    }

    /**
     * @param string|object $for
     *
     * @return string
     */
    final public static function getNameShort($for)
    {
        return (string) static::getReflection($for)->getShortName();
    }

    /**
     * @param string|object $for
     *
     * @return string
     */
    final public static function getNamespace($for)
    {
        return static::getReflection($for)->getNamespaceName();
    }

    /**
     * @param string|object $for
     *
     * @return string[]
     */
    final public static function getNamespaceArray($for)
    {
        return (array) explode('\\', static::getNamespace($for));
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
     * @param mixed $interface
     *
     * @return bool
     */
    final public static function isInterface($interface)
    {
        try {
            return static::assertInterface($interface);
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
        if (is_string($class) && class_exists($class)) {
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
     * @param mixed $interface
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    final public static function assertInterface($interface)
    {
        if (interface_exists($interface)) {
            return true;
        }

        throw new \InvalidArgumentException('Failed asserting passed value is valid interface.');
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
        if (is_string($trait) && trait_exists($trait)) {
            return true;
        }

        throw new \InvalidArgumentException('Failed asserting passed value is valid trait name.');
    }

    /**
     * @param string|object $for
     *
     * @return null|\ReflectionClass|\ReflectionObject
     */
    final public static function getReflection($for)
    {
        if (static::isClass($for)) {
            return new \ReflectionClass($for);
        }

        if (static::isInstance($for)) {
            return new \ReflectionObject($for);
        }

        throw new \InvalidArgumentException(sprintf('Could not create reflection object for "%s"', var_export($for, true)));
    }

    /**
     * @param string|object $class
     *
     * @return bool
     */
    final public static function isThrowableEquitable($class)
    {
        if (static::isInstance($class)) {
            return $class instanceof \Throwable || $class instanceof \Error || $class instanceof \Exception;
        }

        if (static::isClass($class)) {
            $reflection = static::getReflection($class);

            return $reflection->isSubclassOf('\Throwable') || $reflection->isSubclassOf('\Error') || $reflection->isSubclassOf('\Exception');
        }

        return false;
    }
}

/* EOF */
