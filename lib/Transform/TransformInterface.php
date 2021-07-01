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

interface TransformInterface
{
    /**
     * @param mixed|null $value
     */
    public function __construct($value = null, bool $mutable = false);

    /**
     * @return mixed
     */
    public function __toString(): string;

    /**
     * @param mixed|null $value
     *
     * @return static|TransformInterface|StringTransform|NumberTransform
     */
    public static function create($value = null, bool $mutable = false);

    /**
     * @param mixed $value
     *
     * @return TransformInterface
     */
    public function set($value): self;

    /**
     * @return mixed
     */
    public function get();

    public function has(): bool;

    /**
     * @return TransformInterface
     */
    public function setMutable(bool $mutable): self;

    public function isMutable(): bool;

    /**
     * @param mixed $to
     */
    public function isSame($to): bool;

    /**
     * @param mixed $to
     */
    public function isNotSame($to): bool;

    /**
     * @param mixed $to
     */
    public function isEqual($to): bool;

    /**
     * @param mixed $to
     */
    public function isNotEqual($to): bool;

    /**
     * @return TransformInterface
     */
    public function copy(): self;

    /**
     * @return TransformInterface
     */
    public function apply(\Closure $closure): self;
}

/* EOF */
