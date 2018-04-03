<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Interpreter\Backtrace;

use SR\Interpreter\Error\Error;
use SR\Interpreter\Interpreter;
use SR\Utilities\ClassQuery;

final class Backtrace implements \Countable, \IteratorAggregate
{
    /**
     * @var string[]
     */
    private static $blacklistedClasses = [
        self::class,
        BacktraceRecord::class,
        Error::class,
        Interpreter::class,
    ];

    /**
     * @var string[]
     */
    private static $blacklistedUserClasses = [];

    /**
     * @var string[]
     */
    private static $blacklistedPaths = [];

    /**
     * @var array[]
     */
    private $rawData;

    /**
     * @var BacktraceRecord[]
     */
    private $records;

    /**
     * @param int $limit
     */
    public function __construct(int $limit = 20)
    {
        $this->records = self::createBacktraceRecords(
            $this->rawData = self::filterBacktraceRecords($limit)
        );
    }

    /**
     * @param int $limit
     *
     * @return self
     */
    public static function create(int $limit = 20): self
    {
        return new static($limit);
    }

    /**
     * @param string[] ...$classes
     *
     * @return string[]
     */
    public static function addBlacklistedClasses(string ...$classes): array
    {
        self::$blacklistedPaths = [];
        self::$blacklistedUserClasses = array_unique(array_merge(
            self::$blacklistedUserClasses, $classes
        ));

        return array_unique(array_merge(
            self::$blacklistedClasses, self::$blacklistedUserClasses
        ));
    }

    /**
     * @return string[]
     */
    public static function resetBlacklistedClasses(): array
    {
        self::$blacklistedPaths = [];
        self::$blacklistedUserClasses = [];

        return self::$blacklistedClasses;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->records);
    }

    /**
     * @return BacktraceRecord[]
     */
    public function getRecords(): array
    {
        return $this->records;
    }

    /**
     * @return bool
     */
    public function hasRecords(): bool
    {
        return $this->count() > 0;
    }

    /**
     * @return array[]
     */
    public function getRawData(): array
    {
        return $this->rawData;
    }

    /**
     * @return bool
     */
    public function hasRawData(): bool
    {
        return null !== $this->rawData && 0 < count($this->rawData);
    }

    /**
     * @return \Iterator|BacktraceRecord[]
     */
    public function getIterator(): \Iterator
    {
        foreach ($this->records as $index => $record) {
            yield $index => $record;
        }
    }

    /**
     * @param int $limit
     *
     * @return array[]
     */
    private static function filterBacktraceRecords(int $limit): array
    {
        return array_values(
            array_filter(debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, $limit), function (array $data) {
                return false === self::isBacktraceRecordBlacklisted($data['class'] ?? null);
            })
        );
    }

    /**
     * @param string|null $class
     *
     * @return bool
     */
    private static function isBacktraceRecordBlacklisted(string $class = null): bool
    {
        if (empty(self::$blacklistedPaths)) {
            self::$blacklistedPaths = self::resolveBlacklistedPaths();
        }

        if (ClassQuery::isClass($class) && $file = ClassQuery::getReflection($class)->getFileName()) {
            foreach (self::$blacklistedPaths as $path) {
                if (null !== $file && 0 === mb_strpos($file, $path)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return string[]
     */
    private static function resolveBlacklistedPaths(): array
    {
        return array_filter(array_map(function (string $class): ?string {
            return ClassQuery::isClass($class) ? ClassQuery::getReflection($class)->getFileName() : null;
        }, self::addBlacklistedClasses()), function (string $path = null): bool {
            return null !== $path;
        });
    }

    /**
     * @param array $trace
     *
     * @return BacktraceRecord[]
     */
    private static function createBacktraceRecords(array $trace): array
    {
        return array_map(function (array $data): BacktraceRecord {
            return new BacktraceRecord($data);
        }, $trace);
    }
}
