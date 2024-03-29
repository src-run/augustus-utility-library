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

class ValueListReference extends ValueList
{
    /**
     * @var string
     */
    private $reference;

    public function __construct(string $name, string $reference, Package $parent)
    {
        parent::__construct($name, [], $parent);

        $this->reference = $reference;
    }

    public function count(): int
    {
        $this->initialize();

        return parent::count();
    }

    public function isEmpty(): bool
    {
        $this->initialize();

        return parent::isEmpty();
    }

    public function isNotEmpty(): bool
    {
        $this->initialize();

        return parent::isNotEmpty();
    }

    /**
     * @return mixed[]
     */
    public function get(): array
    {
        $this->initialize();

        return parent::get();
    }

    public function each(): \Generator
    {
        $this->initialize();

        return parent::each();
    }

    private function initialize(): void
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
