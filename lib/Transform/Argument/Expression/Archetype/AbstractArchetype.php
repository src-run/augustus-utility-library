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

use SR\Silencer\CallSilencer;

abstract class AbstractArchetype implements ArchetypeInterface
{
    /**
     * @var string
     */
    protected $value;

    /**
     * @param string|null $value
     * @param bool        $negative
     */
    public function __construct(string $value = null)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function get() : string
    {
        return empty($this->value) ? '' : $this->value;
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
