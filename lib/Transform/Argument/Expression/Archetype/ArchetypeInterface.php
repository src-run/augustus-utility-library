<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Util\Transform\Argument\Expression\Archetype;

interface ArchetypeInterface
{
    /**
     * @return string
     */
    public function get() : string;

    /**
     * @return bool
     */
    public function has() : bool;

    /**
     * @return bool
     */
    public function isValid() : bool;
}
