<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Utilities\Transform;

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
     * @param bool       $mutable
     */
    public function __construct($value = null, bool $mutable = false)
    {
        $this->setMutable($mutable);

        if (null !== $value) {
            $this->set($value);
        }
    }

    /**
     * @param null|mixed $value
     * @param bool       $mutable
     *
     * @return static|TransformInterface|StringTransform|NumberTransform
     */
    public static function create($value = null, bool $mutable = false)
    {
        return new static($value, $mutable);
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
     * @return TransformInterface|StringTransform|NumberTransform
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
        return null !== $this->value;
    }

    /**
     * @param bool $mutable
     *
     * @return TransformInterface|StringTransform|NumberTransform
     */
    public function setMutable(bool $mutable) : TransformInterface
    {
        $this->mutable = $mutable;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMutable() : bool
    {
        return true === $this->mutable;
    }

    /**
     * @param mixed $to
     *
     * @return bool
     */
    final public function isSame($to) : bool
    {
        return $this->get() === $to;
    }

    /**
     * @param mixed $to
     *
     * @return bool
     */
    final public function isNotSame($to) : bool
    {
        return false === $this->isSame($to);
    }

    /**
     * @param mixed $to
     *
     * @return bool
     */
    final public function isEqual($to) : bool
    {
        return $this->get() == $to;
    }

    /**
     * @param mixed $to
     *
     * @return bool
     */
    final public function isNotEqual($to) : bool
    {
        return false === $this->isEqual($to);
    }

    /**
     * @return TransformInterface|StringTransform|NumberTransform
     */
    final public function copy() : TransformInterface
    {
        return clone $this;
    }

    /**
     * @param \Closure $closure
     *
     * @return TransformInterface|StringTransform|NumberTransform
     */
    final public function apply(\Closure $closure) : TransformInterface
    {
        return $this->returnInstance(
            $closure->call($bindTo = $this->getWriteContext(), $bindTo)
        );
    }

    /**
     * @param TransformInterface|string|int|float $value
     *
     * @return TransformInterface|StringTransform|NumberTransform
     */
    final protected function returnInstance($value) : TransformInterface
    {
        return $this->getWriteContext()->set(
            $value instanceof TransformInterface ? $value->get() : $value
        );
    }

    /**
     * @return TransformInterface
     */
    private function getWriteContext() : TransformInterface
    {
        return $this->isMutable() ? $this : $this->copy();
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    protected static function isConsumable($value) : bool
    {
        return false === is_array($value) && (false === is_object($value) || is_callable([$value, '__toString']));
    }
}

/* EOF */
