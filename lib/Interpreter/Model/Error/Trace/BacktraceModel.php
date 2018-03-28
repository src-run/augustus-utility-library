<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Interpreter\Model\Error\Trace;

use SR\Interpreter\Interpreter;
use SR\Interpreter\Model\Error\ErrorModel;
use SR\Interpreter\Model\Error\ReportingModel;
use SR\Interpreter\Model\Error\Trace\Record\BacktraceRecordModel;
use SR\Utilities\ClassQuery;

final class BacktraceModel implements \Countable, \IteratorAggregate
{
    /**
     * @var string[]
     */
    private const BLACKLISTED_CLASSES = [
        Interpreter::class,
        ErrorModel::class,
        self::class,
        BacktraceRecordModel::class,
        ReportingModel::class,
    ];

    /**
     * @var array[]
     */
    private $arrayData;

    /**
     * @var BacktraceRecordModel[]
     */
    private $records;

    /**
     * @param int $limit
     */
    public function __construct(int $limit = 20)
    {
        $this->records = self::createBacktraceRecords(
            $this->arrayData = self::filterBacktraceRecords($limit)
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
     * @return array[]
     */
    public function getArrayData(): array
    {
        return $this->arrayData;
    }

    /**
     * @return bool
     */
    public function hasArrayData(): bool
    {
        return null !== $this->getArrayData() && 0 < count($this->getArrayData());
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->records);
    }

    /**
     * @return \Iterator|BacktraceRecordModel[]
     */
    public function getIterator(): \Iterator
    {
        foreach ($this->records as $index => $record) {
            yield $index => $record;
        }
    }

    /**
     * @return BacktraceRecordModel[]
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
     * @param int $limit
     *
     * @return array[]
     */
    private static function filterBacktraceRecords(int $limit): array
    {
        return array_values(array_filter(debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, $limit), function (array $data) {
            return true !== self::isBacktraceRecordBlacklisted($data['file'] ?? null);
        }));
    }

    /**
     * @param string|null $file
     *
     * @return bool
     */
    private static function isBacktraceRecordBlacklisted(string $file = null): bool
    {
        static $paths;

        if (null === $paths) {
            $paths = self::resolveBlacklistedPaths();
        }

        foreach ($paths as $path) {
            if (null !== $file && 0 === mb_strpos($file, $path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string[]
     */
    private static function resolveBlacklistedPaths(): array
    {
        return array_filter(array_map(function (string $className): ?string {
            return dirname(self::resolveClassFilePath($className));
        }, self::BLACKLISTED_CLASSES), function (string $path = null): bool {
            return null !== $path;
        });
    }

    /**
     * @param string $className
     *
     * @return null|string
     */
    private static function resolveClassFilePath(string $className): ?string
    {
        return ClassQuery::isClass($className)
            ? ClassQuery::getReflection($className)->getFileName()
            : null;
    }

    /**
     * @param array $trace
     *
     * @return BacktraceRecordModel[]
     */
    private static function createBacktraceRecords(array $trace): array
    {
        return array_map(function (array $data): BacktraceRecordModel {
            return new BacktraceRecordModel($data);
        }, $trace);
    }
}
