<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Util\Test\Loader\Model;

use SR\Util\Test\Loader\Model\Traits\GenericMethodHelperTrait;
use SR\Util\Test\Loader\Model\Traits\NameAndParentAwareTrait;

class ValueList implements ValueListInterface
{
    use GenericMethodHelperTrait;
    use NameAndParentAwareTrait;

    /**
     * @var mixed[]
     */
    private $data;

    /**
     * @param string  $name
     * @param Package $parent
     * @param array   $data
     */
    public function __construct(string $name, array $data, Package $parent)
    {
        $this->name = $name;
        $this->parent = $parent;

        $this->assignData($data);
    }

    /**
     * @return Package
     */
    public function getParent() : Package
    {
        return $this->parent;
    }

    /**
     * @return int
     */
    public function count() : int
    {
        return count($this->data);
    }

    /**
     * @return bool
     */
    public function isEmpty() : bool
    {
        return 0 === $this->count();
    }

    /**
     * @return bool
     */
    public function isNotEmpty() : bool
    {
        return false === $this->isEmpty();
    }

    /**
     * @return mixed[]
     */
    public function get() : array
    {
        return $this->data;
    }

    /**
     * @return \Generator
     */
    public function each() : \Generator
    {
        return static::runForEach($this->data);
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator() : \ArrayIterator
    {
        return new \ArrayIterator($this->data);
    }

    /**
     * @return bool
     */
    public function isReference() : bool
    {
        return $this instanceof ValueListReference;
    }

    /**
     * @param array $data
     *
     * @return void
     */
    protected function assignData(array $data) : void
    {
        $this->data = $data;
    }
}