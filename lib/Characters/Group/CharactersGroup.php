<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Utilities\Characters\Group;

use SR\Utilities\Characters\CharactersTrait;

final class CharactersGroup implements \Countable, \IteratorAggregate
{
    use CharactersTrait;

    public function __construct(int ...$decimals)
    {
        $this->bytes = $decimals;
    }
}
