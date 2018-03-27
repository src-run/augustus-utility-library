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

class ValueListReference extends ValueList
{
    /**
     * @var string
     */
    private $reference;

    /**
     * @param string  $name
     * @param string  $reference
     * @param Package $parent
     */
    public function __construct(string $name, string $reference, Package $parent)
    {
        parent::__construct($name, [], $parent);

        $this->reference = $reference;
    }

    /**
     * @return int
     */
    public function count() : int
    {
        $this->initialize();

        return parent::count();
    }

    /**
     * @return bool
     */
    public function isEmpty() : bool
    {
        $this->initialize();

        return parent::isEmpty();
    }

    /**
     * @return bool
     */
    public function isNotEmpty() : bool
    {
        $this->initialize();

        return parent::isNotEmpty();
    }

    /**
     * @return mixed[]
     */
    public function get() : array
    {
        $this->initialize();

        return parent::get();
    }

    /**
     * @return \Generator
     */
    public function each() : \Generator
    {
        $this->initialize();

        return parent::each();
    }

    /**
     * @return void
     */
    private function initialize() : void
    {
        if (0 !== parent::count()) {
            return;
        }

        try {
            $this->assignData($this->getParent()->getParent()->findPackage($this->reference)->get($this->getName())->get());
        } catch (\InvalidArgumentException $e) {
            throw new \RuntimeException(sprintf('Unable to resolve reference "%s" for "%s" values in "%s".', $this->reference, $this->getName(), $this->getParent()->getName()));
        }
    }
}