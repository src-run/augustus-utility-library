<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Tests\Utilities\Loader\Model\Traits;

trait GenericMethodHelperTrait
{
    /**
     * @param array|\Countable $elements
     */
    private static function has($elements): bool
    {
        return 0 !== count($elements);
    }

    private static function runForEach(array $elements, string $index = null): \Generator
    {
        foreach ($elements as $i => $v) {
            yield (null === $index ? $i : $index) => $v;
        }
    }

    /**
     * @return mixed|null
     */
    private static function findByName(array $elements, string $name)
    {
        return static::matchSingle($elements, function (NameAwareInterface $nameAware) use ($name) {
            return $name === $nameAware->getName();
        });
    }

    /**
     * @return mixed|null
     */
    private static function matchSingle(array $elements, \Closure $search, string $name = null)
    {
        return static::sanitizeSingleMatch(static::matchMultiple($elements, $search, $name));
    }

    /**
     * @return mixed|null
     */
    private static function sanitizeSingleMatch(array $results)
    {
        if (1 >= ($size = count($results))) {
            return 1 === $size ? $results[0] : null;
        }

        throw new \InvalidArgumentException(sprintf('Failed to narrow down items to a singular match (found %d); try refining your search closure.', $size));
    }

    private static function matchMultiple(array $elements, \Closure $search, string $name = null): array
    {
        return array_values(array_filter($elements, function ($value, $index) use ($search, $name) {
            return $search($value, $index, $name);
        }, ARRAY_FILTER_USE_BOTH));
    }
}
