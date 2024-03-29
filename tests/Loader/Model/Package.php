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
use SR\Tests\Utilities\Loader\Model\Traits\NameAwareInterface;

class Package implements NameAwareInterface, \Countable, \IteratorAggregate
{
    use GenericMethodHelperTrait;
    use NameAndParentAwareTrait;

    /**
     * @var ValueListInterface
     */
    private $arguments;

    /**
     * @var ValueListInterface
     */
    private $provided;

    /**
     * @var ValueListInterface
     */
    private $expected;

    public function __construct(string $name, array $data, Fixture $root)
    {
        $this->name = $name;
        $this->parent = $root;

        $this->extractArguments($data);
        $this->extractProvidedAndExpected($data);
    }

    public function count(): int
    {
        return $this->provided->count();
    }

    public function isGlobal(): bool
    {
        return 'globals' === $this->name;
    }

    public function getParent(): Fixture
    {
        return $this->parent;
    }

    public function get(string $name): ValueListInterface
    {
        if (static::isValidValueListName($name)) {
            return $this->{$name};
        }

        throw new \InvalidArgumentException(sprintf('Invalid get request of "%s" for "%s".', $name, $this->name));
    }

    public function hasArguments(): bool
    {
        return static::has($this->arguments);
    }

    public function getArguments(): ValueListInterface
    {
        return $this->arguments;
    }

    /**
     * @return \Generator|mixed[]|mixed
     */
    public function forEachArgument(): \Generator
    {
        return static::runForEach($this->arguments->get(), $this->getName());
    }

    /**
     * @return mixed[]
     */
    public function matchArgument(\Closure $search): array
    {
        return static::matchMultiple($this->arguments->get(), $search, $this->getName());
    }

    public function hasProvided(): bool
    {
        return $this->provided->isNotEmpty();
    }

    public function getProvided(): ValueListInterface
    {
        return $this->provided;
    }

    /**
     * @return \Generator|mixed[]|mixed
     */
    public function forEachProvided(): \Generator
    {
        return static::runForEach($this->provided->get(), $this->getName());
    }

    /**
     * @return mixed[]
     */
    public function matchProvided(\Closure $search): array
    {
        return static::matchMultiple($this->provided->get(), $search, $this->getName());
    }

    public function hasExpected(): bool
    {
        return $this->expected->isNotEmpty();
    }

    public function getExpected(): ValueListInterface
    {
        return $this->expected;
    }

    /**
     * @return \Generator|mixed[]|mixed
     */
    public function forEachExpected(): \Generator
    {
        return static::runForEach($this->expected->get(), $this->getName());
    }

    /**
     * @return mixed[]
     */
    public function matchExpected(\Closure $search): array
    {
        return static::matchMultiple($this->expected->get(), $search, $this->getName());
    }

    /**
     * @return \Generator|mixed[]
     */
    public function each(): \Generator
    {
        foreach ($this->getIterator() as [$provided, $expected, $arguments]) {
            yield [$provided, $expected, $arguments, $this->getName(), $this];
        }
    }

    /**
     * @return \MultipleIterator|ValueListInterface[]
     */
    public function getIterator(): \MultipleIterator
    {
        $iterator = new \MultipleIterator();
        $iterator->attachIterator($this->provided->each());
        $iterator->attachIterator($this->expected->each());
        if ($this->arguments->count() === $this->provided->count()) {
            $argumentIterator = $this->arguments->each();
        } else {
            $argumentIterator = new \InfiniteIterator(new \ArrayIterator([$this->arguments->get()]));
        }
        $iterator->attachIterator($argumentIterator);

        return $iterator;
    }

    public function hasReference(): bool
    {
        return $this->arguments->isReference() || $this->provided->isReference() || $this->expected->isReference();
    }

    private function extractArguments(array $data): void
    {
        $this->arguments = $this->createInstructionValues($data, 'arguments');
    }

    private function extractProvidedAndExpected(array $data): void
    {
        $this->provided = $this->createInstructionValues($data, 'provided');
        $this->expected = $this->createInstructionValues($data, 'expected');

        if (!$this->isGlobal() && !$this->hasReference() && $this->provided->count() !== $this->expected->count()) {
            throw new \RuntimeException(sprintf('Instruction "%s" provided (%d items) and expected (%d items) must match in length!', $this->name, $this->provided->count(), $this->expected->count()));
        }
    }

    private function createInstructionValues(array $data, string $what): ValueListInterface
    {
        $value = $this->getDataIndexOrGlobal($data, $what);

        if (is_array($value)) {
            return new ValueList($what, $value, $this);
        }

        if (!is_string($value)) {
            throw new \RuntimeException(sprintf('The "%s" instruction data for "%s" in "%s" is unrecignized!', $what, $this->name, $this->getParent()->getFile()));
        }

        if (false === mb_strpos($value, ':')) {
            return new ValueListReference($what, $value, $this);
        }

        return new ValueListReference(...static::parseCustomReferencePointerArguments($what, $value, $this));
    }

    /**
     * @return array|string
     */
    private function getDataIndexOrGlobal(array $data, string $what)
    {
        if (isset($data[$what])) {
            return $data[$what];
        }

        if ($this->isGlobal()) {
            return [];
        }

        try {
            return $this->parent->getGlobals()->get($what)->get();
        } catch (\InvalidArgumentException $e) {
            throw new \RuntimeException(sprintf('Unable to find "%s" in instruction set "%s" from "%s" fixture file.', $what, $this->name, $this->parent->getFile()));
        }
    }

    private static function isValidValueListName(string $name): bool
    {
        return in_array($name, ['arguments', 'provided', 'expected'], true);
    }

    /**
     * @return string[]Package[]
     */
    private static function parseCustomReferencePointerArguments(string $valueListName, string $reference, self $that): array
    {
        [$customReference, $customValueListName] = explode(':', $reference);

        if (static::isValidValueListName($customValueListName)) {
            return [$customValueListName, $customReference, $that];
        }

        return [$valueListName, $reference, $that];
    }
}
