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
    protected mixed $value = null;

    protected bool $mutable;

    public function __construct(mixed $value = null, bool $mutable = false)
    {
        $this->setMutable($mutable);

        if (null !== $value) {
            $this->set($value);
        }
    }

    public function __toString(): string
    {
        return (string) $this->get();
    }

    public static function create(mixed $value = null, bool $mutable = false): TransformInterface|AbstractTransform|NumberTransform|StringTransform
    {
        return new static($value, $mutable);
    }

    public function set(mixed $value): TransformInterface|AbstractTransform|NumberTransform|StringTransform
    {
        $this->value = $value;

        return $this;
    }

    public function get(): mixed
    {
        return $this->value;
    }

    public function has(): bool
    {
        return null !== $this->value;
    }

    public function setMutable(bool $mutable): TransformInterface|AbstractTransform|NumberTransform|StringTransform
    {
        $this->mutable = $mutable;

        return $this;
    }

    public function isMutable(): bool
    {
        return true === $this->mutable;
    }

    final public function isSame(mixed $to): bool
    {
        return $this->get() === $to;
    }

    final public function isNotSame(mixed $to): bool
    {
        return false === $this->isSame($to);
    }

    final public function isEqual(mixed $to): bool
    {
        return (string) $this->get() === (string) $to;
    }

    final public function isNotEqual(mixed $to): bool
    {
        return false === $this->isEqual($to);
    }

    final public function copy(): TransformInterface|AbstractTransform|NumberTransform|StringTransform
    {
        return clone $this;
    }

    final public function apply(\Closure $closure): TransformInterface|AbstractTransform|NumberTransform|StringTransform
    {
        return $this->returnInstance(
            $closure->call($bindTo = $this->getWriteContext(), $bindTo)
        );
    }

    final protected function returnInstance(mixed $value): TransformInterface|AbstractTransform|NumberTransform|StringTransform
    {
        return $this->getWriteContext()->set(
            $value instanceof TransformInterface ? $value->get() : $value
        );
    }

    protected static function isConsumable(mixed $value): bool
    {
        return false === is_array($value) && (false === is_object($value) || is_callable([$value, '__toString']));
    }

    private function getWriteContext(): TransformInterface|AbstractTransform|NumberTransform|StringTransform
    {
        return $this->isMutable() ? $this : $this->copy();
    }
}

/* EOF */
