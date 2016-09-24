<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Transform\Argument\Expression\Representative;

use SR\Transform\Argument\Expression\Archetype\AbstractArchetype;
use SR\Transform\Argument\Expression\Archetype\StringArchetype;
use SR\Transform\Argument\Expression\Archetype\GroupedArchetype;
use SR\Transform\Argument\Expression\Archetype\RangedArchetype;

interface RepresentativeInterface
{
    /**
     * @param bool $caseSensitive
     * @param ArchetypeInterface[] ...$selectors
     */
    public function __construct($caseSensitive = false, ArchetypeInterface ...$selectors);

    /**
     * @return string
     */
    public function regex();

    /**
     * @param ArchetypeInterface $selector
     *
     * @return RepresentativeInterface
     */
    public function add(ArchetypeInterface $selector) : RepresentativeInterface;

    /**
     * @param ArchetypeInterface[] ...$selectors
     *
     * @return RepresentativeInterface
     */
    public function addSelectors(ArchetypeInterface ...$selectors) : RepresentativeInterface;

    /**
     * @param string $value
     *
     * @return RepresentativeInterface
     */
    public function addCharacterSelector(string $value) : RepresentativeInterface;

    /**
     * @param string $value
     * @param bool   $negative
     *
     * @return RepresentativeInterface
     */
    public function addRangeSelector(string $value, bool $negative = false) : RepresentativeInterface;

    /**
     * @param string $value
     * @param bool   $named
     *
     * @return RepresentativeInterface
     */
    public function addGroupSelector(string $value, bool $named = false) : RepresentativeInterface;
}

/* EOF */
