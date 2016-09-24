<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Test;

/**
 * Class AbstractTest.
 */
abstract class AbstractTest extends \PHPUnit_Framework_TestCase
{
    public static $fixtureData = [];

    protected function runThroughAssertions(array $assertions, array $fixtureData = null, $namespace = 'SR\Info\\')
    {
        foreach ($assertions as $call => $opts) {
            $this->runThroughFixtureData($call, $opts, $fixtureData, $namespace);
        }
    }

    protected function runThroughFixtureData($callable, array $assert, array $fixtureData = null, $namespace = 'SR\Info\\')
    {
        if ($fixtureData === null && (!property_exists($this, 'fixtureData') || count(static::$fixtureData) === 0)) {
            $this->fail('Fixture data not defined at either the test class or test method call context.');
        }

        $fixtureData = $fixtureData === null ? static::$fixtureData : $fixtureData;

        foreach ($fixtureData as $i => $data) {
            $parameters = $assert[$i];
            array_unshift($parameters, $data);
            $expected = array_pop($parameters);
            $received = call_user_func_array($namespace.$callable, $parameters);

            $this->assertSame(
                $expected,
                $received,
                sprintf(
                    '"%s(%s)" failure: "expected:%s" !== "received:%s"',
                    $callable,
                    $this->getArrayAsStringRecursive($parameters),
                    $this->getArrayAsStringRecursive($expected),
                    $this->getArrayAsStringRecursive($received)
                )
            );
        }
    }
    
    private function getArrayAsStringRecursive($array)
    {
        if (is_bool($array)) {
            return $array === true ? 'bool:true' : 'bool:false';
        }

        if (!is_array($array)) {
            return $array;
        }

        $string = array_map(function ($value) {
            if (is_array($value)) {
                array_walk_recursive($value, function (&$value) {
                    if (is_array($value)) {
                        $value = implode(',', $value);
                    }
                });

                return sprintf('[%s],', implode(',', $value));
            }

            return sprintf('%s,', $value);
        }, $array);

        $string = implode('', $string);

        return substr($string, 0, strlen($string) - 1);
    }
}

/* EOF */
