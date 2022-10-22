<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Tests\Utilities\Transform;

use SR\Tests\Utilities\AbstractUtilitiesTest;
use SR\Tests\Utilities\Loader\FixtureLoader;
use SR\Tests\Utilities\Loader\Model\Fixture;
use SR\Tests\Utilities\Loader\Model\Package;
use SR\Utilities\Transform\NumberTransform;
use SR\Utilities\Transform\StringTransform;
use SR\Utilities\Transform\TransformInterface;

/**
 * @covers \SR\Utilities\Transform\AbstractTransform
 */
abstract class AbstractTransformTest extends AbstractUtilitiesTest
{
    /**
     * @var string
     */
    protected const FIXTURE_FILE = null;

    /**
     * @var FixtureLoader
     */
    protected static $fixtureLoader;

    public static function setUpBeforeClass(): void
    {
        static::$fixtureLoader = new FixtureLoader();

        if (defined('static::FIXTURE_FILE') && null !== $fixtureFile = static::FIXTURE_FILE) {
            static::$fixtureLoader->load($fixtureFile);
        }
    }

    public function testStaticConstruction()
    {
        $this->assertInstanceOf(TransformInterface::class, call_user_func(sprintf('%s::create', $this->getTargetReflection()->getName())));
    }

    /**
     * @dataProvider provideTestMutatorAndAccessorData
     *
     * @param mixed     $provided
     * @param int|float $expected
     */
    public function testMutatorAndAccessorAndType($provided, $expected)
    {
        $this->assertSame($expected, $this->getTargetInstance($provided)->get());
    }

    abstract public function provideTestMutatorAndAccessorData(): \Generator;

    /**
     * @dataProvider provideTestConstructorExceptionOnInvalidValueData
     */
    public function testConstructorExceptionOnInvalidValue($provided)
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->getTargetInstance($provided);
    }

    abstract public function provideTestConstructorExceptionOnInvalidValueData(): \Generator;

    protected function getFixture(): Fixture
    {
        return static::$fixtureLoader->load(static::FIXTURE_FILE);
    }

    protected function getTargetReflection(): ?\ReflectionClass
    {
        if (0 !== count($target = $this->getFixture()->getTargets())) {
            return $target[0];
        }

        return null;
    }

    /**
     * @return object|TransformInterface|NumberTransform|StringTransform
     */
    protected function getTargetInstance(...$arguments): TransformInterface
    {
        $target = $this->getTargetReflection()->getName();

        return new $target(...$arguments);
    }

    /**
     * Load YML data provider/config for test runner.
     */
    protected function getPackageForMethod(string $method): Package
    {
        $name = lcfirst(str_replace('test', '', $method));

        $package = $this->getFixture()->findPackage($name);

        if (!$package->hasProvided() || !$package->hasExpected()) {
            $this->doFail($name, 'Missing either provided or expected fixture data.');
        }

        if ($package->getProvided()->count() !== $package->getExpected()->count()) {
            $this->doFail($name, 'Provided and expected fixture data must contain equal list lengths!');
        }

        return $package;
    }

    /**
     * Initialize runner for method context.
     */
    protected function initRunner(string $method)
    {
        foreach ($this->getPackageForMethod($method)->each() as $i => [$provided, $expected, $arguments, $name, $package]) {
            $this->runnerAssert($package, $i, $provided, $expected, $arguments, $name);
        }
    }

    /**
     * Perform runner assertion tests.
     */
    protected function runnerAssert(Package $package, mixed $iteration, mixed $provided, mixed $expected, array $arguments, string $method): void
    {
        $template = sprintf('Set "%d" for "%s" context.', $iteration, $method);

        if (is_bool($expected)) {
            $this->runBooleanCheck($provided, $expected, $arguments, $method);

            return;
        }

        $this->assertTransformConstruct($provided, $template, '1/4');
        $instance = $this->getTargetInstance($provided);
        $callable = [$instance, $method];

        $this->assertTransform($instance, $provided, $instance->get(), $template, '2/4');
        $this->assertTransform($instance, $expected, call_user_func_array($callable, $arguments), $template, '3/4');
        $this->assertTransform($instance, $expected, call_user_func_array($callable, $arguments), $template, '4/4');

        $this->runnerAssertCustom($package, $iteration, $provided, $expected, $arguments, $method, $callable);
    }

    protected function runBooleanCheck($provided, $expected, $arguments, $method)
    {
        $inst = $this->getTargetInstance($provided);
        $call = [$inst, $method];
        $ret = call_user_func_array($call, $arguments);

        $this->assertSame($expected, $ret);
    }

    protected function runnerAssertCustom(Package $package, int $iteration, mixed $provided, mixed $expected, array $arguments, string $method, callable $callable): void
    {
    }

    /**
     * @param string|int $expected
     */
    protected function assertTransformConstruct($expected, string $message, string $which)
    {
        /** @var TransformInterface $instance */
        $instance = $this->getTargetInstance();

        $this->assertFalse($instance->has());

        $instance = $this->getTargetInstance($expected);
        $received = $instance->get();

        if ($instance instanceof NumberTransform) {
            $expected = $this->readyComparisonValueForNumberTransformResult($expected);
            $received = $this->readyComparisonValueForNumberTransformResult($received);
        }

        $this->assertInstanceOf(TransformInterface::class, $instance);
        $this->assertSame($expected, $received, $this->equalsMessage($message, $which, $expected, $received));
    }

    protected function assertTransform($instance, mixed $expected, mixed $received, string $message, string $which): void
    {
        $message = $this->equalsMessage($message, $which, $expected, $received);

        if ($received instanceof TransformInterface) {
            $received = $received->get();
        }

        if ($instance instanceof NumberTransform) {
            $expected = $this->readyComparisonValueForNumberTransformResult($expected);
            $received = $this->readyComparisonValueForNumberTransformResult($received);
        }

        $this->assertSame($expected, $received, $message);
    }

    protected function equalsMessage(string $message, string $which, string $expected, string $received): string
    {
        return sprintf('%s (test %s) [asserting "%s" === "%s"]', $message, $which, $expected, $received);
    }

    /**
     * @param array ...$replacements
     */
    protected function doFail(string $context = null, string $message, ...$replacements): void
    {
        if (0 !== count($replacements)) {
            $message = vsprintf($message, $replacements);
        }

        if (null !== $context) {
            $message .= sprintf(' ("%s" test case)', $context);
        }

        $this->fail($message);
    }

    /**
     * @param string|float|int $value
     *
     * @return float|int
     */
    private function readyComparisonValueForNumberTransformResult($value)
    {
        $i = $f = $value;
        $i = (int) $i;
        $f = (float) $f;

        return $i === $f ? $i : $f;
    }
}
