<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Tests\Utilities\Loader;

use SR\Tests\Utilities\Loader\Model\Fixture;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class FixtureLoader
{
    /**
     * @var string
     */
    private const DEFAULT_ROOT = __DIR__ . '/../Resources/Fixtures/';

    /**
     * @var bool
     */
    private $fresh;

    /**
     * @var string
     */
    private $rootPath;

    /**
     * @var Fixture[]
     */
    private static $cache;

    public function __construct(bool $fresh = false, string $rootPath = null)
    {
        $this->fresh = $fresh;
        $this->rootPath = $rootPath;
    }

    public function load(string $file): Fixture
    {
        if (!$this->isLoaded($file)) {
            $this->set($file, new Fixture($this->readData($file), $file));
        }

        return $this->get($file);
    }

    public function isLoaded(string $file): bool
    {
        return isset(static::$cache[$this->getCacheIndex($file)]);
    }

    private function set(string $file, Fixture $fixture): void
    {
        static::$cache[$this->getCacheIndex($file)] = $fixture;
    }

    private function get(string $file): Fixture
    {
        return static::$cache[$this->getCacheIndex($file)];
    }

    private function getCacheIndex(string $file): string
    {
        return md5(__CLASS__ . $this->rootPath . $file);
    }

    private function buildFilePath(string $file): string
    {
        if (false === $real = realpath($this->rootPath ?? static::DEFAULT_ROOT)) {
            throw new \InvalidArgumentException(sprintf('Unable to resolve fixture root path "%s"', $file));
        }

        $absolute = $real . DIRECTORY_SEPARATOR . $file;

        if (false === file_exists($absolute) || false === is_readable($absolute)) {
            throw new \InvalidArgumentException(sprintf('Fixture data file "%s" does not exist or is not readable!', $absolute));
        }

        return $absolute;
    }

    /**
     * @return mixed[]
     */
    private function readData(string $file): array
    {
        if (!is_readable($absolute = $this->buildFilePath($file)) || false === $contents = file_get_contents($absolute)) {
            throw new \InvalidArgumentException(sprintf('Unable to read file contents of "%s"', $absolute));
        }

        try {
            return Yaml::parse($contents, Yaml::PARSE_EXCEPTION_ON_INVALID_TYPE | Yaml::PARSE_CUSTOM_TAGS);
        } catch (ParseException $e) {
            throw new \RuntimeException(sprintf('Unable to parse fixture YAML data in "%s"', $absolute), null, $e);
        }
    }
}
