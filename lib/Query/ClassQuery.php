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

final class ClassQuery
{
    /**
     * @param string|mixed|object $for
     */
    public static function getName($for, bool $qualified = true): string
    {
        return $qualified ? static::getNameQualified($for) : static::getNameShort($for);
    }

    /**
     * @param string|mixed|object $for
     */
    public static function getNameQualified($for): string
    {
        return (string) static::getReflection($for)->getName();
    }

    /**
     * @param string|mixed|object $for
     */
    public static function getNameShort($for): string
    {
        return (string) static::getReflection($for)->getShortName();
    }

    /**
     * @param string|mixed|object $for
     */
    public static function getNamespace($for): string
    {
        return static::getReflection($for)->getNamespaceName();
    }

    /**
     * @param string|mixed|object $for
     *
     * @return string[]
     */
    public static function getNamespaceArray($for): array
    {
        return (array) explode('\\', static::getNamespace($for));
    }

    /**
     * @param mixed $class
     */
    public static function isClass($class): bool
    {
        try {
            return static::assertClass($class);
        } catch (\InvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * @param mixed $instance
     */
    public static function isInstance($instance): bool
    {
        try {
            return static::assertInstance($instance);
        } catch (\InvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * @param string|mixed $interface
     */
    public static function isInterface($interface): bool
    {
        try {
            return static::assertInterface($interface);
        } catch (\InvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * @param string|mixed $trait
     */
    public static function isTrait($trait): bool
    {
        try {
            return static::assertTrait($trait);
        } catch (\InvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * @param string|mixed $class
     *
     * @throws \InvalidArgumentException
     */
    public static function assertClass($class): bool
    {
        if (is_string($class) && @class_exists($class)) {
            return true;
        }

        throw new \InvalidArgumentException('Failed asserting passed value is valid class name.');
    }

    /**
     * @param string|mixed|object $instance
     *
     * @throws \InvalidArgumentException
     */
    public static function assertInstance($instance): bool
    {
        if (!is_string($instance) && is_object($instance)) {
            return true;
        }

        throw new \InvalidArgumentException('Failed asserting passed value is an object instance.');
    }

    /**
     * @param string|mixed $interface
     *
     * @throws \InvalidArgumentException
     */
    public static function assertInterface($interface): bool
    {
        if (is_string($interface) && @interface_exists($interface)) {
            return true;
        }

        throw new \InvalidArgumentException('Failed asserting passed value is valid interface.');
    }

    /**
     * @param string|mixed $trait
     *
     * @throws \InvalidArgumentException
     */
    public static function assertTrait($trait): bool
    {
        if (is_string($trait) && @trait_exists($trait)) {
            return true;
        }

        throw new \InvalidArgumentException('Failed asserting passed value is valid trait name.');
    }

    /**
     * @param $target
     */
    public static function isReflectable($target): bool
    {
        try {
            static::getReflection($target);
        } catch (\InvalidArgumentException $e) {
            return false;
        }

        return true;
    }

    /**
     * @param string|mixed|object $target
     */
    public static function tryReflection($target): ?\ReflectionClass
    {
        return static::isReflectable($target) ? static::getReflection($target) : null;
    }

    /**
     * @param string|mixed|object $target
     *
     * @return \ReflectionClass|\ReflectionObject
     */
    public static function getReflection($target): \ReflectionClass
    {
        try {
            return static::isInstance($target) ? new \ReflectionObject($target) : new \ReflectionClass($target);
        } catch (\ReflectionException $e) {
            throw new \InvalidArgumentException(sprintf('Could not create reflection object for "%s"', @print_r($target, true)), 0, $e);
        }
    }

    /**
     * @param string|mixed|object $class
     */
    public static function isThrowableEquitable($class): bool
    {
        if (static::isInstance($class)) {
            return $class instanceof \Throwable
                || $class instanceof \Error
                || $class instanceof \Exception;
        }

        if (static::isClass($class)) {
            $reflection = static::getReflection($class);

            return $reflection->isSubclassOf('\Throwable')
                || $reflection->isSubclassOf('\Error')
                || $reflection->isSubclassOf('\Exception');
        }

        return false;
    }

    /**
     * @param object|string $from
     */
    public static function getNonAccessibleMethodReflection(string $method, $from): \ReflectionMethod
    {
        ($method = static::getReflection($from)->getMethod($method))
            ->setAccessible(true)
    ;

        return $method;
    }

    /**
     * @param object|string $from
     * @param array         ...$arguments
     *
     * @return mixed
     */
    public static function getNonAccessibleMethodInvokeReturn(string $method, $from, ...$arguments)
    {
        return static::getNonAccessibleMethodReflection($method, $from)->invokeArgs($from, $arguments);
    }

    /**
     * @param object|string $from
     */
    public static function getNonAccessiblePropertyReflection(string $property, $from): \ReflectionProperty
    {
        ($property = static::getReflection($from)->getProperty($property))
            ->setAccessible(true)
    ;

        return $property;
    }

    /**
     * @param object|string $from
     *
     * @return mixed
     */
    public static function getNonAccessiblePropertyValue(string $property, $from)
    {
        return static::getNonAccessiblePropertyReflection($property, $from)->getValue($from);
    }

    /**
     * @param object|string $from
     * @param mixed         $value
     */
    public static function setNonAccessiblePropertyValue(string $property, $from, $value): void
    {
        static::getNonAccessiblePropertyReflection($property, $from)->setValue($from, $value);
    }
}
