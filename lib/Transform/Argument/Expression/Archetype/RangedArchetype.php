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

class RangedArchetype extends AbstractArchetype
{
    /**
     * @var string
     */
    protected $negative;

    /**
     * @param string|null $value
     * @param bool        $negative
     */
    public function __construct(string $value = null, bool $negative = false)
    {
        $this->negative = $negative;
        parent::__construct($value);
    }

    /**
     * @param string|null $value
     *
     * @return ArchetypeInterface
     */
    public function set(string $value = null, bool $negative = false) : ArchetypeInterface
    {
        $this->negative = $negative;

        return parent::set($value);
    }

    /**
     * @return string
     */
    public function get() : string
    {
        if (empty($value = parent::get())) {
            return '';
        }

        return sprintf('[%s%s]', $this->negative ? '^' : '', $value);
    }
}

/* EOF */
