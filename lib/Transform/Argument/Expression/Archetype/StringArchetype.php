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

use SR\Silencer\CallSilencer;

class StringArchetype implements ArchetypeInterface
{
    /**
     * @var string
     */
    protected $value;

    /**
     * @param string|null $value
     *
     * @return StringArchetype
     */
    public function set(string $value = null) : ArchetypeInterface
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function get() : string
    {
        return null === $this->value ? '' : $this->value;
    }

    /**
     * @return bool
     */
    public function has() : bool
    {
        return $this->value !== null && count($this->value) > 0;
    }

    /**
     * @return bool
     */
    public function isValid() : bool
    {
        if (!$this->has()) {
            return false;
        }

        return CallSilencer::create(
            function () {
                return preg_match(sprintf('{%s}', $this->get()), null);
            },
            function ($result) {
                return $result !== false;
            })
            ->invoke()
            ->isResultValid();
    }
}

/* EOF */
