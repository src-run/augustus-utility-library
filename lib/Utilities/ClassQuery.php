<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Utilities;

final class ClassQuery
{
    /**
     * @param string|mixed|object $for
     * @param bool                $qualified
     *
     * @return string
     */
    public static function getName($for, bool $qualified = true): string
    {
        return $qualified ? static::getNameQualified($for) : static::getNameShort($for);
    }

    /**
     * @param string|mixed|object $for
     *
     * @return string
     */
    public static function getNameQualified($for): string
    {
        return (string) static::getReflection($for)->getName();
    }

    /**
     * @param string|mixed|object $for
     *
     * @return string
     */
    public static function getNameShort($for): string
    {
        return (string) static::getReflection($for)->getShortName();
    }

    /**
     * @param string|mixed|object $for
     *
     * @return string
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
     *
     * @return bool
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
     *
     * @return bool
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
     *
     * @return bool
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
     *
     * @return bool
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
     *
     * @return bool
     */
    public static function assertClass($class): bool
    {
        if (is_string($class) && class_exists($class)) {
            return true;
        }

        throw new \InvalidArgumentException('Failed asserting passed value is valid class name.');
    }

    /**
     * @param string|mixed|object $instance
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public static function assertInstance($instance): bool
    {
        if (is_object($instance)) {
            return true;
        }

        throw new \InvalidArgumentException('Failed asserting passed value is an object instance.');
    }

    /**
     * @param string|mixed $interface
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public static function assertInterface($interface): bool
    {
        if (interface_exists($interface)) {
            return true;
        }

        throw new \InvalidArgumentException('Failed asserting passed value is valid interface.');
    }

    /**
     * @param string|mixed $trait
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public static function assertTrait($trait): bool
    {
        if (is_string($trait) && trait_exists($trait)) {
            return true;
        }

        throw new \InvalidArgumentException('Failed asserting passed value is valid trait name.');
    }

    /**
     * @param string|mixed|object $for
     *
     * @return null|\ReflectionClass|\ReflectionObject
     */
    public static function getReflection($for): ?\ReflectionClass
    {
        try {
            if (static::isInstance($for)) {
                return new \ReflectionObject($for);
            }

            return new \ReflectionClass($for);
        } catch (\ReflectionException $e) {
        }

        throw new \InvalidArgumentException(sprintf(
            'Could not create reflection object for "%s"', @print_r($for, true)
        ), 0, $e ?? null);
    }

    /**
     * @param string|mixed|object $class
     *
     * @return bool
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
     * @param string        $method
     * @param object|string $from
     *
     * @return \ReflectionMethod
     */
    public static function getNonAccessibleMethodReflection(string $method, $from): \ReflectionMethod
    {
        ($method = static::getReflection($from)->getMethod($method))
            ->setAccessible(true);

        return $method;
    }

    /**
     * @param string        $method
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
     * @param string        $property
     * @param object|string $from
     *
     * @return \ReflectionProperty
     */
    public static function getNonAccessiblePropertyReflection(string $property, $from): \ReflectionProperty
    {
        ($property = static::getReflection($from)->getProperty($property))
            ->setAccessible(true);

        return $property;
    }

    /**
     * @param string        $property
     * @param object|string $from
     *
     * @return mixed
     */
    public static function getNonAccessiblePropertyValue(string $property, $from)
    {
        return static::getNonAccessiblePropertyReflection($property, $from)->getValue($from);
    }

    /**
     * @param string        $property
     * @param object|string $from
     * @param mixed         $value
     */
    public static function setNonAccessiblePropertyValue(string $property, $from, $value): void
    {
        static::getNonAccessiblePropertyReflection($property, $from)->setValue($from, $value);
    }
}
