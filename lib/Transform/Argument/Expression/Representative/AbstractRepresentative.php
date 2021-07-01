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

    public function add(ArchetypeInterface $selector): RepresentativeInterface
    {
        $this->selectors[] = $selector;

        return $this;
    }

    /**
     * @param ArchetypeInterface[] ...$selectors
     */
    public function addSelectors(ArchetypeInterface ...$selectors): RepresentativeInterface
    {
        foreach ($selectors as $s) {
            $this->add($s);
        }

        return $this;
    }

    /**
     * @param bool $enabled
     */
    public function setCaseSensitivity($enabled = false): RepresentativeInterface
    {
        $this->caseSensitivity = $enabled;

        return $this;
    }

    public function isCaseSensitive(): bool
    {
        return $this->caseSensitivity;
    }

    public function enableAnchorLeft(): SearchReplaceRepresentative
    {
        $this->anchorLeft = true;

        return $this;
    }

    public function enableAnchorRight(): SearchReplaceRepresentative
    {
        $this->anchorRight = true;

        return $this;
    }

    public function isAnchoredLeft(): bool
    {
        return true === $this->anchorLeft;
    }

    public function isAnchoredRight(): bool
    {
        return true === $this->anchorRight;
    }
}

/* EOF */
