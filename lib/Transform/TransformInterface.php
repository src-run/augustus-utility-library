<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Util\Transform;

interface TransformInterface
{
    /**
     * @param null|mixed $value
     * @param bool       $mutable
     */
    public function __construct($value = null, bool $mutable = false);

    /**
     * @param null|mixed $value
     * @param bool       $mutable
     *
     * @return static|TransformInterface|StringTransform|NumberTransform
     */
    public static function create($value = null, bool $mutable = false);

    /**
     * @return mixed
     */
    public function __toString() : string;

    /**
     * @param mixed $value
     *
     * @return TransformInterface
     */
    public function set($value) : TransformInterface;

    /**
     * @return mixed
     */
    public function get();

    /**
     * @return bool
     */
    public function has() : bool;

    /**
     * @param bool $mutable
     *
     * @return TransformInterface
     */
    public function setMutable(bool $mutable) : TransformInterface;

    /**
     * @return bool
     */
    public function isMutable() : bool;

    /**
     * @param mixed $to
     *
     * @return bool
     */
    public function isSame($to) : bool;

    /**
     * @param mixed $to
     *
     * @return bool
     */
    public function isNotSame($to) : bool;

    /**
     * @param mixed $to
     *
     * @return bool
     */
    public function isEqual($to) : bool;

    /**
     * @param mixed $to
     *
     * @return bool
     */
    public function isNotEqual($to) : bool;

    /**
     * @return TransformInterface
     */
    public function copy() : TransformInterface;

    /**
     * @param \Closure $closure
     *
     * @return TransformInterface
     */
    public function apply(\Closure $closure) : TransformInterface;
}

/* EOF */
