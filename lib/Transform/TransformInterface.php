<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Transform;

interface TransformInterface
{
    /**
     * @param null|mixed $value
     */
    public function __construct($value = null);

    /**
     * @param mixed $value
     *
     * @return TransformInterface
     */
    public function create($value) : TransformInterface;

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
     * @param \Closure $closure
     * @param bool     $clone
     *
     * @return TransformInterface
     */
    public function apply(\Closure $closure, $clone = true) : TransformInterface;

    /**
     * @param int|float $comparison
     *
     * @return bool
     */
    public function isSame($comparison) : bool;

    /**
     * @return string[]
     */
    public function split() : array;
}

/* EOF */
