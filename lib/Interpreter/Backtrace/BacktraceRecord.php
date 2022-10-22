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

use SR\Utilities\Query\ClassQuery;

final class BacktraceRecord
{
    /**
     * @var mixed[]
     */
    private $arrayData;

    /**
     * @var \SplFileInfo
     */
    private $file;

    /**
     * @var int
     */
    private $line;

    /**
     * @var string
     */
    private $funcName;

    /**
     * @var \ReflectionFunctionAbstract|\ReflectionFunction|\ReflectionMethod|null
     */
    private $funcReflection;

    /**
     * @var string
     */
    private $funcCallType;

    /**
     * @var mixed[]
     */
    private $arguments;

    /**
     * @var string
     */
    private $className;

    /**
     * @var object
     */
    private $objectInstance;

    /**
     * @var \ReflectionClass|\ReflectionObject|null
     */
    private $objectReflection;

    public function __construct(array $record)
    {
        [
            $this->arrayData, $this->line, $this->funcName, $this->funcCallType, $this->arguments, $this->className,
            $this->objectInstance, $this->objectReflection, $this->funcReflection, $this->file,
        ] = self::extractBacktraceRecordData($record);
    }

    public function __toString(): string
    {
        return $this->stringify();
    }

    /**
     * @return mixed[]
     */
    public function getArrayData(): array
    {
        return $this->arrayData;
    }

    public function getFile(): ?\SplFileInfo
    {
        return $this->file;
    }

    public function hasFile(): bool
    {
        return null !== $this->getFile();
    }

    public function getLine(): ?int
    {
        return $this->line;
    }

    public function hasLine(): bool
    {
        return null !== $this->getLine();
    }

    public function getFuncName(): ?string
    {
        return $this->funcName;
    }

    public function hasFuncName(): bool
    {
        return null !== $this->getFuncName();
    }

    public function getFuncReflection(): \ReflectionFunctionAbstract|\ReflectionFunction|\ReflectionMethod|null
    {
        return $this->funcReflection;
    }

    public function hasFuncReflection(): bool
    {
        return null !== $this->getFuncReflection();
    }

    public function getFuncCallType(): ?string
    {
        return $this->funcCallType;
    }

    public function hasFuncCallType(): bool
    {
        return null !== $this->getFuncCallType();
    }

    public function isFuncCallTypeStatic(): bool
    {
        return '::' === $this->getFuncCallType();
    }

    public function isFuncCallTypeInstance(): bool
    {
        return '->' === $this->getFuncCallType();
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function hasArguments(): bool
    {
        return 0 < count($this->getArguments());
    }

    public function getClassName(): ?string
    {
        return $this->className;
    }

    public function hasClassName(): bool
    {
        return null !== $this->getClassName();
    }

    public function getObjectInstance(): ?object
    {
        return $this->objectInstance;
    }

    public function hasObjectInstance(): bool
    {
        return null !== $this->getObjectInstance();
    }

    public function getObjectReflection(): \ReflectionClass|\ReflectionObject|null
    {
        return $this->objectReflection;
    }

    public function hasObjectReflection(): bool
    {
        return null !== $this->getObjectReflection();
    }

    public function getType(): string
    {
        if ($this->hasObjectInstance()) {
            return 'object';
        }

        if ($this->hasClassName()) {
            return 'class';
        }

        return 'function';
    }

    public function stringify(): string
    {
        $s = '';

        if ($this->hasClassName()) {
            $s .= sprintf('%s%s', $this->getClassName(), $this->hasFuncCallType() ? $this->getFuncCallType() : '::');
        }

        $s .= sprintf('%s(%s)', $this->getFuncName(), $this->hasArguments() ? implode(', ', array_map(function ($argument): string {
            return self::stringifyArguments($argument);
        }, $this->getArguments())) : '');

        if ($this->hasFile() || $this->hasLine()) {
            $s .= ' [';
        }

        if ($this->hasFile()) {
            $s .= sprintf('%s', $this->getFile()->getPathname());
        }

        if ($this->hasLine()) {
            $s .= sprintf('@%d', $this->getLine());
        }

        if ($this->hasFile() || $this->hasLine()) {
            $s .= ']';
        }

        return $s . sprintf(' (%s)', $this->getType());
    }

    private static function stringifyArguments(mixed $argument): string
    {
        if (is_object($argument)) {
            return sprintf('%s:%s', gettype($argument), get_class($argument));
        }

        return is_scalar($argument)
            ? $argument
            : self::stringifyComplex($argument);
    }

    private static function stringifyComplex(mixed $complex): string
    {
        return trim(preg_replace('{\s+}', ' ',
            preg_replace('{\n[\s\t]*}', ' ', @print_r($complex, true))
        ), ' ');
    }

    private static function extractBacktraceRecordData(array $record): array
    {
        return array_merge([
            $record,
            self::extractBacktraceLine($record),
            $func = self::extractBacktraceFunc($record),
            self::extractBacktraceType($record),
            self::extractBacktraceArgs($record),
            $name = self::extractBacktraceName($record),
            $inst = self::extractBacktraceInst($record),
            $nRef = self::extractBacktraceNameReflection($inst, $name),
            self::extractBacktraceFuncReflection($nRef, $func),
            self::extractBacktraceFile($record, $nRef),
        ]);
    }

    private static function extractBacktraceFile(array $data, \ReflectionClass $class = null): ?\SplFileInfo
    {
        $resolved = $data['file'] ?? ($class?->getFileName());

        return $resolved ? new \SplFileInfo($resolved) : null;
    }

    private static function extractBacktraceLine(array $data): ?string
    {
        return $data['line'] ?? null;
    }

    private static function extractBacktraceFunc(array $data): ?string
    {
        return $data['function'] ?? null;
    }

    private static function extractBacktraceType(array $data): ?string
    {
        return $data['type'] ?? null;
    }

    private static function extractBacktraceArgs(array $data): array
    {
        return array_map(function ($value): string {
            return self::stringifyArguments($value);
        }, $data['args'] ?? []);
    }

    private static function extractBacktraceName(array $data): ?string
    {
        return $data['class'] ?? null;
    }

    private static function extractBacktraceInst(array $data): ?object
    {
        return $data['object'] ?? null;
    }

    private static function extractBacktraceNameReflection($object = null, string $class = null): \ReflectionClass|\ReflectionObject|null
    {
        if (ClassQuery::isInstance($object)) {
            return ClassQuery::getReflection($object);
        }

        if (ClassQuery::isClass($class)) {
            return ClassQuery::getReflection($class);
        }

        return null;
    }

    private static function extractBacktraceFuncReflection(\ReflectionClass $classReflection = null, string $function = null): ?\ReflectionFunctionAbstract
    {
        if (null !== $classReflection && $classReflection->hasMethod($function)) {
            return $classReflection->getMethod($function);
        }

        if (null === $function) {
            return null;
        }

        try {
            return new \ReflectionFunction($function);
        } catch (\ReflectionException $e) {
            return null;
        }
    }
}
