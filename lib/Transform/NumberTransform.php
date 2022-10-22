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

class NumberTransform extends AbstractTransform
{
    public function __construct(mixed $number = null, bool $mutable = false)
    {
        parent::__construct($number, $mutable);
    }

    public function set(mixed $value): self
    {
        if (false === static::isConsumable($value)) {
            throw new \InvalidArgumentException('Value is not an integer or float and could not be coerced to either.');
        }

        return parent::set(static::castToIntegerOrFloat($value));
    }

    public function toInteger(): self
    {
        return $this->apply(function () {
            return (int) $this->get();
        });
    }

    public function isInteger(): bool
    {
        return is_int($this->get());
    }

    public function toFloat(): self
    {
        return $this->apply(function () {
            return (float) $this->get();
        });
    }

    public function isFloat(): bool
    {
        return is_float($this->get());
    }

    public function round(int $precision = 2): self
    {
        return $this->apply(function () {
            return round($this->get());
        });
    }

    public function increment(float|int $by = 1): self
    {
        return $this->apply(function () use ($by) {
            return $this->get() + $by;
        });
    }

    public function decrement(float|int $by = 1): self
    {
        return $this->apply(function () use ($by) {
            return $this->get() - $by;
        });
    }

    public function multiply(float|int $by): self
    {
        return $this->apply(function () use ($by) {
            return $this->get() * $by;
        });
    }

    public function divide(float|int $by): self
    {
        return $this->apply(function () use ($by) {
            return $this->get() / $by;
        });
    }

    private static function castToIntegerOrFloat(string $value): float|int
    {
        $i = (int) $value;
        $f = (float) $value;

        return (float) $i === (float) $f ? $i : $f;
    }
}
