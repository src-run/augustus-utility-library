<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Utilities\Test\Loader\Model\Traits;

trait GenericMethodHelperTrait
{
    /**
     * @param array|\Countable $elements
     *
     * @return bool
     */
    private static function has($elements) : bool
    {
        return 0 !== count($elements);
    }

    /**
     * @param array       $elements
     * @param string|null $index
     *
     * @return \Generator
     */
    private static function runForEach(array $elements, string $index = null) : \Generator
    {
        foreach ($elements as $i => $v) {
            yield (null === $index ? $i : $index) => $v;
        }
    }

    /**
     * @param array  $elements
     * @param string $name
     *
     * @return mixed|null
     */
    private static function findByName(array $elements, string $name)
    {
        return static::matchSingle($elements, function (NameAwareInterface $nameAware) use ($name) {
            return $name === $nameAware->getName();
        });
    }

    /**
     * @param array       $elements
     * @param \Closure    $search
     * @param string|null $name
     *
     * @return mixed|null
     */
    private static function matchSingle(array $elements, \Closure $search, string $name = null)
    {
        return static::sanitizeSingleMatch(static::matchMultiple($elements, $search, $name));
    }

    /**
     * @param array $results
     *
     * @return mixed|null
     */
    private static function sanitizeSingleMatch(array $results)
    {
        if (1 >= ($size = count($results))) {
            return 1 === $size ? $results[0] : null;
        }

        throw new \InvalidArgumentException(
            sprintf('Failed to narrow down items to a singular match (found %d); try refining your search closure.', $size)
        );
    }

    /**
     * @param array       $elements
     * @param \Closure    $search
     * @param string|null $name
     *
     * @return array
     */
    private static function matchMultiple(array $elements, \Closure $search, string $name = null) : array
    {
        return array_values(array_filter($elements, function ($value, $index) use ($search, $name) {
            return $search($value, $index, $name);
        }, ARRAY_FILTER_USE_BOTH));
    }
}