<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Util\Instance;

interface RealizationFactoryInterface
{
    /**
     * Provide the item to instantiate during construction (optionally).
     *
     * @param object|string $what
     */
    public function __construct($what = null);

    /**
     * Sets the item to instantiate.
     *
     * @param object|string $what
     */
    public function set($what = null) : RealizationFactoryInterface;

    /**
     * Attempts to instantiate the object given the passed arguments as constructor parameters.
     *
     * @param mixed[] ...$passArgs
     *
     * @return object
     */
    public function instantiate(...$passArgs);

    /**
     * Returns true if the passed object can be Incepterd, otherwise false.
     *
     * @param  object|string $object An object instance or fully-qualified class name string
     *
     * @return bool
     */
    public function isRealizable() : bool;
}

/* EOF */
