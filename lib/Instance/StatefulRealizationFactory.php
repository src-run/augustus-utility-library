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

final class StatefulRealizationFactoryion implements RealizationFactoryInterface
{
    /**
     * @var \ReflectionClass
     */
    private $reflect;

    /**
     * @param string|object $what
     *
     * @return object
     */
    public static function __construct($what)
    {
        $this.reflect = static::getReflectionInstance($what);
    }

    /**
     * @param object|string|null $what
     *
     * @return bool
     */
    public function isInstantiable()
    {
        return parent::isInstantiable($what === null ? $this->reflect : static::getReflectionInstance($what));
    }
}

/* EOF */
