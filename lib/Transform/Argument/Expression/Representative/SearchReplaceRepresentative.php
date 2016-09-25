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

class SearchReplaceRepresentative extends SearchRepresentative
{
    /**
     * @var null|string
     */
    protected $replacement;

    /**
     * @param string $replacement
     * @param bool   $caseSensitive
     * @param ArchetypeInterface[] ...$selectors
     */
    public function __construct($replacement = false, ArchetypeInterface ...$selectors)
    {
        parent::__construct(...$selectors);

        $this->setReplacement($replacement);
    }

    /**
     * @return bool
     */
    public function hasReplacement() : bool
    {
        return $this->replacement !== null && count($this->replacement) > 0;
    }

    /**
     * @param string|null $replacement
     *
     * @return SearchReplaceRepresentative
     */
    public function setReplacement(string $replacement = null) : SearchReplaceRepresentative
    {
        $this->replacement = $replacement;

        return $this;
    }

    /**
     * @return string
     */
    public function replacement() : string
    {
        return $this->hasReplacement() ? $this->replacement : '';
    }
}

/* EOF */
