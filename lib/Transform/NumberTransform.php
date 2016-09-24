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
}
