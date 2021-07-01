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

class SearchReplaceRepresentative extends SearchRepresentative
{
    /**
     * @var string|null
     */
    protected $replacement;

    /**
     * @param string               $replacement
     * @param bool                 $caseSensitive
     * @param ArchetypeInterface[] ...$selectors
     */
    public function __construct($replacement = '', ArchetypeInterface ...$selectors)
    {
        parent::__construct(...$selectors);

        $this->setReplacement($replacement);
    }

    public function hasReplacement(): bool
    {
        return null !== $this->replacement && mb_strlen($this->replacement) > 0;
    }

    /**
     * @return SearchReplaceRepresentative
     */
    public function setReplacement(string $replacement = null): self
    {
        $this->replacement = $replacement;

        return $this;
    }

    public function replacement(): string
    {
        return $this->hasReplacement() ? $this->replacement : '';
    }
}

/* EOF */
