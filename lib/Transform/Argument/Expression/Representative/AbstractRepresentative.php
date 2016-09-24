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

use SR\Transform\Argument\Expression\Archetype\ArchetypeInterface;
use SR\Transform\Argument\Expression\Archetype\StringArchetype;
use SR\Transform\Argument\Expression\Archetype\GroupedArchetype;
use SR\Transform\Argument\Expression\Archetype\RangedArchetype;

abstract class AbstractRepresentative implements RepresentativeInterface
{
    /**
     * @var ArchetypeInterface[]
     */
    protected $selectors;

    /**
     * @var bool
     */
    protected $caseSensitive = true;

    /**
     * @return string
     */
    public function regex()
    {
        $expressions = array_map(function (ArchetypeInterface $value) {
            return $value->get();
        }, $this->selectors);

        return sprintf('{%s}%s', implode('', $expressions), $this->isCaseSensitive() ? '' : 'i');
    }

    /**
     * @param ArchetypeInterface $selector
     *
     * @return RepresentativeInterface
     */
    public function add(ArchetypeInterface $selector) : RepresentativeInterface
    {
        $this->selectors[] = $selector;

        return $this;
    }

    /**
     * @param ArchetypeInterface[] ...$selectors
     *
     * @return RepresentativeInterface
     */
    public function addSelectors(ArchetypeInterface ...$selectors) : RepresentativeInterface
    {
        foreach ($selectors as $s) {
            $this->add($s);
        }

        return $this;
    }

    /**
     * @param string $value
     *
     * @return RepresentativeInterface
     */
    public function addCharacterSelector(string $value) : RepresentativeInterface
    {
        return $this->add(new StringArchetype($value));
    }

    /**
     * @param string $value
     * @param bool   $negative
     *
     * @return RepresentativeInterface
     */
    public function addRangeSelector(string $value, bool $negative = false) : RepresentativeInterface
    {
        return $this->add(new RangedArchetype($value, $negative));
    }

    /**
     * @param string $value
     * @param bool   $named
     *
     * @return RepresentativeInterface
     */
    public function addGroupSelector(string $value, bool $named = false) : RepresentativeInterface
    {
        return $this->add(new GroupedArchetype($value, $named));
    }

    /**
     * @param  bool $enabled
     *
     * @return RepresentativeInterface
     */
    public function setCaseSensitivity($enabled = false) : RepresentativeInterface
    {
        $this->caseSensitivity = $enabled;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCaseSensitive() : bool
    {
        return $this->caseSensitivity;
    }

    /**
     * @return RepresentativeInterface
     */
    public function enableCaseSensitivity() : RepresentativeInterface
    {
        return $this->setCaseSentitivity(true);
    }

    /**
     * @return RepresentativeInterface
     */
    public function disableCaseSensitivity() : RepresentativeInterface
    {
        return $this->setCaseSentitivity(false);
    }
}

/* EOF */
