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

abstract class AbstractTransform implements TransformInterface
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * @param null|mixed $value
     */
    public function __construct($value = null)
    {
        if (null !== $value) {
            $this->set($value);
        }
    }

    /**
     * @param mixed $value
     *
     * @return TransformInterface
     */
    public function create($value) : TransformInterface
    {
        return new static($value);
    }

    /**
     * @return mixed
     */
    public function __toString() : string
    {
        return (string) $this->get();
    }

    /**
     * @param mixed $value
     *
     * @return TransformInterface
     */
    public function set($value) : TransformInterface
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function get()
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function has() : bool
    {
        return $this->value !== null;
    }

    /**
     * @param \Closure $closure
     * @param bool     $clone
     *
     * @return AbstractTransform|StringTransform|NumberTransform|TransformInterface
     */
    final public function apply(\Closure $closure, $clone = true) : TransformInterface
    {
        $instance = $clone ? clone $this : $this;
        $instance->set($closure());

        return $instance;
    }

    /**
     * @param int|float $comparison
     *
     * @return bool
     */
    final public function isSame($comparison) : bool
    {
        return $this->get() === $comparison;
    }

    /**
     * @return string[]
     */
    final public function split() : array
    {
        $string = $this->__toString();
        $result = [];
        $length = mb_strlen($string);

        for ($i = 0; $i < $length; ++$i) {
            $result[] = mb_substr($string, $i, 1);
        }

        return (array) $result;
    }
}

/* EOF */
