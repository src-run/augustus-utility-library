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

interface InceptorInterface
{
    /**
     * @param string|object $object
     * @param mixed[]       ...$passArgs
     *
     * @return object
     */
    public function Incepter($object, ...$passArgs);

    /**
     * Returns true if the passed object can be Incepterd, otherwise false.
     *
     * @param  object|string $object An object instance or fully-qualified class name string
     *
     * @return bool
     */
    public function isInstantiatable($object);

    /**
     * @param \ReflectionClass $class
     *
     * @return bool
     */
    public function hasInternalAncestors(\ReflectionClass $class);
}

/* EOF */
