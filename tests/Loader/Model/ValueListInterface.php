<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Tests\Utilities\Loader\Model;

use SR\Tests\Utilities\Loader\Model\Traits\NameAwareInterface;

interface ValueListInterface extends NameAwareInterface, \Countable, \IteratorAggregate
{
    public function getName(): string;

    public function getParent(): Package;

    public function count(): int;

    public function isEmpty(): bool;

    public function isNotEmpty(): bool;

    /**
     * @return mixed[]
     */
    public function get(): array;

    public function each(): \Generator;

    public function getIterator(): \ArrayIterator;

    public function isReference(): bool;
}
