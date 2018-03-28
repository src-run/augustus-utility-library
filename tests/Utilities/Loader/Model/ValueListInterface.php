<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Utilities\Test\Loader\Model;

use SR\Utilities\Test\Loader\Model\Traits\NameAwareInterface;

interface ValueListInterface extends NameAwareInterface, \Countable, \IteratorAggregate
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return Package
     */
    public function getParent(): Package;

    /**
     * @return int
     */
    public function count(): int;

    /**
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * @return bool
     */
    public function isNotEmpty(): bool;

    /**
     * @return mixed[]
     */
    public function get(): array;

    /**
     * @return \Generator
     */
    public function each(): \Generator;

    /**
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator;

    /**
     * @return bool
     */
    public function isReference(): bool;
}
