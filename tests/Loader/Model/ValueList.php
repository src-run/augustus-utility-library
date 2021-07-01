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

use SR\Tests\Utilities\Loader\Model\Traits\GenericMethodHelperTrait;
use SR\Tests\Utilities\Loader\Model\Traits\NameAndParentAwareTrait;

class ValueList implements ValueListInterface
{
    use GenericMethodHelperTrait;
    use NameAndParentAwareTrait;

    /**
     * @var mixed[]
     */
    private $data;

    public function __construct(string $name, array $data, Package $parent)
    {
        $this->name = $name;
        $this->parent = $parent;

        $this->assignData($data);
    }

    public function getParent(): Package
    {
        return $this->parent;
    }

    public function count(): int
    {
        return count($this->data);
    }

    public function isEmpty(): bool
    {
        return 0 === $this->count();
    }

    public function isNotEmpty(): bool
    {
        return false === $this->isEmpty();
    }

    /**
     * @return mixed[]
     */
    public function get(): array
    {
        return $this->data;
    }

    public function each(): \Generator
    {
        return static::runForEach($this->data);
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->data);
    }

    public function isReference(): bool
    {
        return $this instanceof ValueListReference;
    }

    protected function assignData(array $data): void
    {
        $this->data = $data;
    }
}
