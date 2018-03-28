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

use SR\Utilities\Test\Loader\Model\Traits\GenericMethodHelperTrait;

class Fixture
{
    use GenericMethodHelperTrait;

    /**
     * @var string[]
     */
    private static $syntaxVersionsSupported = [
        '2.0',
    ];

    /**
     * @var string
     */
    private $file;

    /**
     * @var string
     */
    private $syntax;

    /**
     * @var Package
     */
    private $globals;

    /**
     * @var \ReflectionClass[]
     */
    private $targets = [];

    /**
     * @var Package[]
     */
    private $packages = [];

    /**
     * @param array  $data
     * @param string $file
     */
    public function __construct(array $data, string $file)
    {
        $this->file = $file;

        $this->extractSyntax($data);
        $this->extractTargets($data);
        $this->extractPackages($data);
    }

    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * @return string
     */
    public function getSyntax(): string
    {
        return $this->syntax;
    }

    /**
     * @return Package
     */
    public function getGlobals(): Package
    {
        return $this->globals;
    }

    /**
     * @return bool
     */
    public function hasTargets(): bool
    {
        return static::has($this->targets);
    }

    /**
     * @return \ReflectionClass[]
     */
    public function getTargets(): array
    {
        return $this->targets;
    }

    /**
     * @return \Generator|\ReflectionClass[]|\ReflectionClass
     */
    public function forEachTarget(): \Generator
    {
        return static::runForEach($this->targets);
    }

    /**
     * @param string $name
     *
     * @return \ReflectionClass|null
     */
    public function findTarget(string $name): ?\ReflectionClass
    {
        return static::findByName($this->targets, $name);
    }

    /**
     * @return bool
     */
    public function hasPackages(): bool
    {
        return static::has($this->packages);
    }

    /**
     * @return Package[]
     */
    public function getPackages(): array
    {
        return $this->packages;
    }

    /**
     * @return \Generator|Package[]|Package
     */
    public function forEachPackage(): \Generator
    {
        return static::runForEach($this->packages);
    }

    /**
     * @return Package[]
     */
    public function matchPackage(\Closure $search): array
    {
        return static::matchMultiple($this->packages, $search);
    }

    /**
     * @param string $name
     *
     * @return Package|null
     */
    public function findPackage(string $name): ?Package
    {
        return static::findByName($this->packages, $name);
    }

    /**
     * @param array $data
     */
    private function extractSyntax(array $data): void
    {
        if (false === isset($data['syntax_version'])) {
            throw new \RuntimeException(sprintf('Fixture "%s" must contain a root "syntax_version" key.', $this->file));
        }

        if (false === in_array($syntaxVersion = $data['syntax_version'], static::$syntaxVersionsSupported, false)) {
            throw new \RuntimeException(sprintf('Fixture "%s" has unsupported version "%s" (requires: %s).', $this->file, $syntaxVersion, implode(', ', static::$syntaxVersionsSupported)));
        }

        $this->syntax = $syntaxVersion;
    }

    /**
     * @param array $data
     */
    private function extractTargets(array $data): void
    {
        $this->targets = array_map(function (string $targetClass) {
            return $this->processTargetClass($targetClass);
        }, isset($data['target_classes']) ? $data['target_classes'] : []);
    }

    /**
     * @param string $targetClass
     *
     * @return \ReflectionClass
     */
    private function processTargetClass(string $targetClass): \ReflectionClass
    {
        try {
            return new \ReflectionClass($targetClass);
        } catch (\ReflectionException $e) {
            throw new \RuntimeException(sprintf(
                'Unable to create reflection class for "%s" as fixture file "%s" target class.', $targetClass, $this->file
            ), null, $e);
        }
    }

    /**
     * @param array $data
     */
    private function extractPackages(array $data): void
    {
        $root = isset($data['instructions']) ? $data['instructions'] : [];
        $this->globals = $this->processGlobalInstructions(isset($root['globals']) ? $root['globals'] : []);
        $this->packages = $this->processMethodInstructions(isset($root['methods']) ? $root['methods'] : []);
    }

    /**
     * @param array $globals
     *
     * @return Package
     */
    private function processGlobalInstructions(array $globals): Package
    {
        return $this->processInstruction('globals', $globals);
    }

    /**
     * @param array $methods
     *
     * @return Package[]
     */
    private function processMethodInstructions(array $methods): array
    {
        if (0 < count($methods)) {
            array_walk($methods, function (array &$data, string $name) {
                $data = $this->processInstruction($name, $data);
            });
        }

        return $methods;
    }

    /**
     * @param string $name
     * @param array  $data
     *
     * @return Package
     */
    private function processInstruction(string $name, array $data): Package
    {
        return new Package($name, $data, $this);
    }
}
