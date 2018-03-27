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

final class NumberTransform extends AbstractTransform
{
    /**
     * Construct by optionally setting the number to manipulate.
     *
     * @param int|float|null $number
     * @param bool           $mutable
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
     * @throws \InvalidArgumentException If a non integer/float is provided.
     *
     * @return NumberTransform
     */
    public function set($value) : TransformInterface
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
    final public function toInteger() : NumberTransform
    {
        return $this->apply(function () {
            return (int) $this->get();
        });
    }

    /**
     * @return bool
     */
    final public function isInteger() : bool
    {
        return is_int($this->get());
    }

    /**
     * Convert number to float.
     *
     * @return NumberTransform|AbstractTransform
     */
    final public function toFloat() : NumberTransform
    {
        return $this->apply(function () {
            return (float) $this->get();
        });
    }

    /**
     * @return bool
     */
    final public function isFloat() : bool
    {
        return is_float($this->get());
    }

    /**
     * @param int $precision
     *
     * @return NumberTransform|AbstractTransform
     */
    final public function round($precision = 2)
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
    final public function increment($by = 1)
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
    final public function decrement($by = 1)
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
    final public function multiply($by)
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
    final public function divide($by)
    {
        return $this->apply(function () use ($by) {
            return $this->get() / $by;
        });
    }

    /**
     * @param string $value
     *
     * @return float|int
     */
    private static function castToIntegerOrFloat(string $value)
    {
        $i = (int) $value;
        $f = (float) $value;

        return $i == $f ? $i : $f;
    }
}
