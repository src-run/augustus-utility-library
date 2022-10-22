<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Utilities\Transform\Argument\Expression\Representative;

use SR\Utilities\Transform\Argument\Expression\Archetype\ArchetypeInterface;

interface RepresentativeInterface
{
    /**
     * @return string
     */
    public function regex();

    public function add(ArchetypeInterface $selector): self;

    /**
     * @param ArchetypeInterface[] ...$selectors
     */
    public function addSelectors(ArchetypeInterface ...$selectors): self;
}

/* EOF */
