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

abstract class AbstractTransform implements TransformInterface
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var bool
     */
    protected $mutable;

    /**
     * @param null|mixed $value
     */
    public function __construct($value = null)
    {
        $this->mutable = false;

        if (null !== $value) {
            $this->set($value);
        }
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
     * @return TransformInterface
     */
    public function enableMutable() : TransformInterface
    {
        $this->mutable = true;

        return $this;
    }

    /**
     * @return TransformInterface
     */
    public function disableMutable() : TransformInterface
    {
        $this->mutable = false;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMutable() : bool
    {
        return $this->mutable === true;
    }

    /**
     * @return TransformInterface
     */
    final public function copy() : TransformInterface
    {
        return clone $this;
    }

    /**
     * @param \Closure $closure
     *
     * @return AbstractTransform|StringTransform|NumberTransform|TransformInterface
     */
    final public function apply(\Closure $closure) : TransformInterface
    {
        return $this->invokeClosure($closure);
    }

    /**
     * @param \Closure $closure
     *
     * @return TransformInterface
     */
    final protected function invokeClosure(\Closure $closure) : TransformInterface
    {
        $bindTo = $this->isMutable() ? $this : clone $this;
        $result = $closure->call($bindTo, $bindTo);

        return $this->readyResult($result);
    }

    /**
     * @param TransformInterface|mixed $value
     *
     * @return TransformInterface
     */
    final protected function readyResult($value) : TransformInterface
    {
        $result = $value instanceof TransformInterface ? $value->get() : $value;
        $object = $this->isMutable() ? $this : $this->copy();

        return $object->set($result);
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
