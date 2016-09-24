<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Transform\Argument\Expression\Archetype;

class GroupedArchetype extends StringArchetype
{
    /**
     * @var bool
     */
    protected $named;

    /**
     * @param string|null $value
     * @param bool        $named
     */
    public function __construct(string $value = null, bool $named = false)
    {
        $this->named = $named;
        parent::__construct($value);
    }

    /**
     * @return string
     */
    public function get() : string
    {
        if (empty($value = parent::get())) {
            return '';
        }

        return sprintf('(%s%s)', ($this->named ? '' : '?:'), $value);
    }
}

/* EOF */
