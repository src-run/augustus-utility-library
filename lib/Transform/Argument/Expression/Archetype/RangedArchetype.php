<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Utilities\Transform\Argument\Expression\Archetype;

class RangedArchetype extends AbstractArchetype
{
    /**
     * @var string
     */
    protected $negative;

    public function __construct(string $value = null, bool $negative = false)
    {
        $this->negative = $negative;

        parent::__construct($value);
    }

    public function set(string $value = null, bool $negative = false): ArchetypeInterface
    {
        $this->negative = $negative;
        $this->value = $value;

        return $this;
    }

    public function get(): string
    {
        return sprintf('[%s%s]', $this->negative ? '^' : '', empty($this->value) ? '' : $this->value);
    }
}

/* EOF */
