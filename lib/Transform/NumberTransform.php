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

final class NumberTransform extends AbstractTransform
{
    /**
     * Construct by optionally setting the number to manipulate.
     *
     * @param int|null $number
     */
    public function __construct($number = null)
    {
        if (null === $number) {
            return;
        }

        parent::__construct($number);
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
    final public function multiply($by = 1)
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
    final public function divide($by = 1)
    {
        return $this->apply(function () use ($by) {
            return $this->get() / $by;
        });
    }

    /**
     * @param int      $base
     * @param int      $toBase
     * @param int|null $precision
     * @param bool     $baseIsMaxInteger
     *
     * @return AbstractTransform|NumberTransform|StringTransform
     */
    final public function toBase($base, $toBase, $precision = null, $baseIsMaxInteger = false)
    {
        return $this->apply(function () use ($base, $toBase, $precision, $baseIsMaxInteger) {
            if (0 === $base) {
                throw new \InvalidArgumentException('Cannot convert from a base of zero.');
            }

            $converted = $this->get() * $toBase / $base;

            if (null !== $precision) {
                $converted = round($converted, $precision);
            }

            if ($precision === 0) {
                $converted = (int) $converted;
            }

            if ($baseIsMaxInteger && $converted > $toBase) {
                return $toBase;
            }

            return $converted;
        });
    }
}
