<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Util\Transform\Argument\Expression\Representative;

use SR\Util\Transform\Argument\Expression\Archetype\ArchetypeInterface;
use SR\Util\Transform\Argument\Expression\Archetype\RangedArchetype;
use SR\Util\Transform\Argument\Expression\Archetype\StringArchetype;

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
     * @var bool
     */
    protected $anchorRight = false;

    /**
     * @var bool
     */
    protected $anchorLeft = false;

    /**
     * @return string
     */
    public function regex()
    {
        $expressions = array_filter($this->selectors, function (ArchetypeInterface $value) {
            return $value->isValid();
        });

        $expressions = array_map(function (ArchetypeInterface $value) {
            return $value->get();
        }, $expressions);

        return sprintf(
            '{%s%s%s}%s',
            $this->isAnchoredLeft() ? '^' : '',
            implode('', $expressions),
            $this->isAnchoredRight() ? '$' : '',
            $this->isCaseSensitive() ? '' : 'i'
        );
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
     * @param bool $enabled
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

    /**
     * @return SearchReplaceRepresentative
     */
    public function enableAnchorLeft() : SearchReplaceRepresentative
    {
        $this->anchorLeft = true;

        return $this;
    }

    /**
     * @return SearchReplaceRepresentative
     */
    public function enableAnchorRight() : SearchReplaceRepresentative
    {
        $this->anchorRight = true;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAnchoredLeft() : bool
    {
        return $this->anchorLeft == true;
    }

    /**
     * @return bool
     */
    public function isAnchoredRight() : bool
    {
        return $this->anchorRight == true;
    }
}

/* EOF */
