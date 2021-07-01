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
    /**
     * Construct by optionally setting the number to manipulate.
     *
     * @param int|float|null $number
     */
    public function __construct($number = null, bool $mutable = false)
    {
        if (null !== $number) {
            parent::__construct($number, $mutable);
        } else {
            $this->setMutable($mutable);
        }
    }

    /**
     * @param int|float $value
     *
     * @throws \InvalidArgumentException if a non integer/float is provided
     *
     * @return NumberTransform
     */
    public function set($value): TransformInterface
    {
        if (false === static::isConsumable($value)) {
            throw new \InvalidArgumentException('Value is not an integer or float and could not be coerced to either.');
        }

        return parent::set(static::castToIntegerOrFloat($value));
    }

    /**
     * @return int|float
     */
    public function get()
    {
        return parent::get();
    }

    /**
     * Convert number to integer.
     *
     * @return NumberTransform|AbstractTransform
     */
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

    /**
     * Convert number to float.
     *
     * @return NumberTransform|AbstractTransform
     */
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

    /**
     * @param int $precision
     *
     * @return NumberTransform|AbstractTransform
     */
    public function round($precision = 2)
    {
        return $this->apply(function () {
            return round($this->get());
        });
    }

    /**
     * @param int $by
     *
     * @return NumberTransform|AbstractTransform
     */
    public function increment($by = 1)
    {
        return $this->apply(function () use ($by) {
            return $this->get() + $by;
        });
    }

    /**
     * @param int $by
     *
     * @return NumberTransform|AbstractTransform
     */
    public function decrement($by = 1)
    {
        return $this->apply(function () use ($by) {
            return $this->get() - $by;
        });
    }

    /**
     * @param int $by
     *
     * @return NumberTransform|AbstractTransform
     */
    public function multiply($by)
    {
        return $this->apply(function () use ($by) {
            return $this->get() * $by;
        });
    }

    /**
     * @param int $by
     *
     * @return NumberTransform|AbstractTransform
     */
    public function divide($by)
    {
        return $this->apply(function () use ($by) {
            return $this->get() / $by;
        });
    }

    /**
     * @return float|int
     */
    private static function castToIntegerOrFloat(string $value)
    {
        $i = (int) $value;
        $f = (float) $value;

        return (float) $i === (float) $f ? $i : $f;
    }
}
