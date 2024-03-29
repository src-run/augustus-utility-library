<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Utilities\Interpreter\Backtrace;

use SR\Utilities\Interpreter\Error\Error;
use SR\Utilities\Interpreter\Interpreter;
use SR\Utilities\Query\ClassQuery;

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

    public function __construct(int $limit = 20)
    {
        $this->records = self::createBacktraceRecords(
            $this->rawData = self::filterBacktraceRecords($limit)
        );
    }

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
     * @return BacktraceRecord[]
     */
    private static function createBacktraceRecords(array $trace): array
    {
        return array_map(function (array $data): BacktraceRecord {
            return new BacktraceRecord($data);
        }, $trace);
    }
}
