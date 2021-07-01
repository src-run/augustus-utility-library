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

use SR\Silencer\CallSilencerFactory;

abstract class AbstractArchetype implements ArchetypeInterface
{
    /**
     * @var string
     */
    protected $value;

    /**
     * @param bool $negative
     */
    public function __construct(string $value = null)
    {
        $this->value = $value;
    }

    public function get(): string
    {
        return empty($this->value) ? '' : $this->value;
    }

    public function has(): bool
    {
        return null !== $this->value && mb_strlen($this->value) > 0;
    }

    public function isValid(): bool
    {
        if (!$this->has()) {
            return false;
        }

        $return = CallSilencerFactory::create(function () {
            return preg_match(sprintf('{%s}', $this->get()), null);
        })->setValidator(function ($result) {
            return false !== $result;
        })->invoke();

        return $return->isValid();
    }
}

/* EOF */
