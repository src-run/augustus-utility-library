<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Util\Test\Transform;

use SR\Util\Test\AbstractTest;
use SR\Util\Transform\TransformInterface;
use Symfony\Component\Yaml\Yaml;

abstract class AbstractTransformTest extends AbstractTest
{
    protected $providerYmlFilePath;

    /**
     * Load YML data provider/config for test runner.
     *
     * @param string $method
     *
     * @return mixed[]
     */
    protected function loadYmlData($method)
    {
        $method = lcfirst(str_replace('test', '', $method));
        $config = Yaml::parse(file_get_contents($this->providerYmlFilePath))['data'];

        $target = $config['target'];

        if (!class_exists($target)) {
            $this->fail(sprintf('Target class does not exist "%s"', $target));
        }

        $args = isset($config['argument'][$method]) ? $config['argument'][$method] : [];
        $inputs = isset($config['provided'][$method]) ? $config['provided'][$method] : $config['provided']['global'];
        $expect = isset($config['expected'][$method]) ? $config['expected'][$method] : null;

        if ($inputs === null || $expect === null || count($inputs) !== count($expect)) {
            $this->fail(sprintf('Incorrect fixture data for method "%s"', $method));
        }

        return [$target, $method, $args, $expect, $inputs];
    }

    /**
     * Instantiate runner target object.
     *
     * @param string  $name
     * @param mixed[] $args
     *
     * @return object
     */
    protected function instantiateTarget($name, $args = [])
    {
        $reflect = new \ReflectionClass($name);

        if (!$reflect->isInstantiable()) {
            $this->fail(sprintf('Target is not instantiable "%s"', $target));
        }

        return $reflect->newInstanceArgs($args);
    }

    /**
     * Initialize runner for method context.
     *
     * @param string $method
     */
    protected function initRunner($method)
    {
        list($target, $method, $args, $expect, $inputs) = $this->loadYmlData($method);

        foreach ($inputs as $i => $v) {
            $this->runnerAssert($i, $v, $expect[$i], isset($args[$i]) ? $args[$i] : [], $target, $method);
        }
    }

    /**
     * Perform runner assertion tests.
     *
     * @param int     $iteration
     * @param mixed[] $input
     * @param mixed[] $expect
     * @param string  $target
     * @param string  $method
     * @param mixed[] $argument
     */
    protected function runnerAssert($iteration, $input, $expect, $args, $target, $method)
    {
        $template = sprintf('Set "%d" for "%s" context.', $iteration, $method);

        $this->assertTransformConstruct($target, $input, $template, '1/4');

        $instance = $this->instantiateTarget($target, [$input]);
        $callable = [$instance, $method];

        $this->assertTransform($input, $instance->get(), $template, '2/4');
        $this->assertTransform($expect, call_user_func_array($callable, $args)->get(), $template, '3/4');
        $this->assertTransform($expect, call_user_func_array($callable, $args), $template, '3/4');

        $this->runnerAssertCustom($iteration, $input, $expect, $args, $target, $method, $instance, $callable);
    }

    protected function assertTransformConstruct(string $target, $input, $message, $which)
    {
        $instance = new $target();

        $this->assertFalse($instance->has());

        $instance = new $target($input);
        $recieved = $instance->get();

        $this->assertInstanceOf(TransformInterface::class, $instance);
        $this->assertSame($input, $recieved, $this->equalsMessage($message, $which, $input, $recieved));
    }

    protected function assertTransform($expected, $recieved, $message, $which)
    {
        $message = $this->equalsMessage($message, $which, $expected, $recieved);

        $this->assertSame((string) $expected, (string) $recieved, $message);
    }

    protected function equalsMessage($message, $which, $expected, $recieved)
    {
        return sprintf('%s (test %s) [asserting "%s" === "%s"]', $message, $which, $expected, $recieved);
    }

    protected function runnerAssertCustom($iteration, $input, $expect, $args, $target, $method, $instance, $callable)
    {
    }
}
