<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Interpreter\Model\Error\Trace\Record;

use SR\Utilities\ClassInfo;

final class BacktraceRecordModel
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
     * @var null|\ReflectionFunctionAbstract|\ReflectionFunction|\ReflectionMethod
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
     * @var null|\ReflectionClass|\ReflectionObject
     */
    private $objectReflection;

    /**
     * @param array $record
     */
    public function __construct(array $record)
    {
        [
            $this->arrayData, $this->line, $this->funcName, $this->funcCallType, $this->arguments, $this->className,
            $this->objectInstance, $this->objectReflection, $this->funcReflection, $this->file,
        ] = self::extractBacktraceRecordData($record);
    }

    /**
     * @return string
     */
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

    /**
     * @return null|\SplFileInfo
     */
    public function getFile(): ?\SplFileInfo
    {
        return $this->file;
    }

    /**
     * @return bool
     */
    public function hasFile(): bool
    {
        return null !== $this->getFile();
    }

    /**
     * @return null|int
     */
    public function getLine(): ?int
    {
        return $this->line;
    }

    /**
     * @return bool
     */
    public function hasLine(): bool
    {
        return null !== $this->getLine();
    }

    /**
     * @return null|string
     */
    public function getFuncName(): ?string
    {
        return $this->funcName;
    }

    /**
     * @return bool
     */
    public function hasFuncName(): bool
    {
        return null !== $this->getFuncName();
    }

    /**
     * @return null|\ReflectionFunctionAbstract|\ReflectionFunction|\ReflectionMethod
     */
    public function getFuncReflection(): ?\ReflectionFunctionAbstract
    {
        return $this->funcReflection;
    }

    /**
     * @return bool
     */
    public function hasFuncReflection(): bool
    {
        return null !== $this->getFuncReflection();
    }

    /**
     * @return null|string
     */
    public function getFuncCallType(): ?string
    {
        return $this->funcCallType;
    }

    /**
     * @return bool
     */
    public function hasFuncCallType(): bool
    {
        return null !== $this->getFuncCallType();
    }

    /**
     * @return bool
     */
    public function isFuncCallTypeStatic(): bool
    {
        return '::' === $this->getFuncCallType();
    }

    /**
     * @return bool
     */
    public function isFuncCallTypeInstance(): bool
    {
        return '->' === $this->getFuncCallType();
    }

    /**
     * @return mixed[]
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @return bool
     */
    public function hasArguments(): bool
    {
        return 0 < count($this->getArguments());
    }

    /**
     * @return null|string
     */
    public function getClassName(): ?string
    {
        return $this->className;
    }

    /**
     * @return bool
     */
    public function hasClassName(): bool
    {
        return null !== $this->getClassName();
    }

    /**
     * @return null|object
     */
    public function getObjectInstance()
    {
        return $this->objectInstance;
    }

    /**
     * @return bool
     */
    public function hasObjectInstance()
    {
        return null !== $this->getObjectInstance();
    }

    /**
     * @return null|\ReflectionClass|\ReflectionObject
     */
    public function getObjectReflection(): ?\ReflectionClass
    {
        return $this->objectReflection;
    }

    /**
     * @return bool
     */
    public function hasObjectReflection(): bool
    {
        return null !== $this->getObjectReflection();
    }

    /**
     * @return string
     */
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

    /**
     * @return string
     */
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

        return $s.sprintf(' (%s)', $this->getType());
    }

    /**
     * @param mixed $argument
     *
     * @return string
     */
    private static function stringifyArguments($argument): string
    {
        if (is_object($argument)) {
            return sprintf('%s:%s', gettype($argument), get_class($argument));
        }

        return is_scalar($argument)
            ? $argument
            : self::stringifyComplex($argument);
    }

    /**
     * @param mixed $complex
     *
     * @return string
     */
    private static function stringifyComplex($complex): string
    {
        return trim(preg_replace('{\s+}', ' ',
            preg_replace('{\n[\s\t]*}', ' ', @print_r($complex, true))
        ), ' ');
    }

    /**
     * @param array $record
     *
     * @return array
     */
    private static function extractBacktraceRecordData(array $record): array
    {
        return array_merge([
            $record,
            $line = self::extractBacktraceLine($record),
            $func = self::extractBacktraceFunc($record),
            $type = self::extractBacktraceType($record),
            $args = self::extractBacktraceArgs($record),
            $name = self::extractBacktraceName($record),
            $inst = self::extractBacktraceInst($record),
            $nRef = self::extractBacktraceNameReflection($inst, $name),
            $fRef = self::extractBacktraceFuncReflection($nRef, $func),
            $file = self::extractBacktraceFile($record, $nRef, $fRef),
        ]);
    }

    /**
     * @param array $data
     *
     * @return null|\SplFileInfo
     */
    private static function extractBacktraceFile(array $data, \ReflectionClass $class = null, \ReflectionFunctionAbstract $function = null): ?\SplFileInfo
    {
        if (isset($data['file'])) {
            $resolved = $data['file'];
        } elseif (null !== $class) {
            $resolved = $class->getFileName();
        }

        return isset($resolved) ? new \SplFileInfo($resolved) : null;
    }

    /**
     * @param array $data
     *
     * @return null|string
     */
    private static function extractBacktraceLine(array $data): ?string
    {
        return $data['line'] ?? null;
    }

    /**
     * @param array $data
     *
     * @return null|string
     */
    private static function extractBacktraceFunc(array $data): ?string
    {
        return $data['function'] ?? null;
    }

    /**
     * @param array $data
     *
     * @return null|string
     */
    private static function extractBacktraceType(array $data): ?string
    {
        return $data['type'] ?? null;
    }

    /**
     * @param array $data
     *
     * @return mixed[]
     */
    private static function extractBacktraceArgs(array $data): array
    {
        return array_map(function ($value): string {
            return self::stringifyArguments($value);
        }, $data['args'] ?? []);
    }

    /**
     * @param array $data
     *
     * @return null|string
     */
    private static function extractBacktraceName(array $data): ?string
    {
        return $data['class'] ?? null;
    }

    /**
     * @param array $data
     *
     * @return null|object
     */
    private static function extractBacktraceInst(array $data)
    {
        return $data['object'] ?? null;
    }

    /**
     * @param object|null $object
     * @param string|null $class
     *
     * @return null|\ReflectionClass|\ReflectionObject
     */
    private static function extractBacktraceNameReflection($object = null, string $class = null): ?\ReflectionClass
    {
        if (ClassInfo::isInstance($object)) {
            return ClassInfo::getReflection($object);
        }

        if (ClassInfo::isClass($class)) {
            return ClassInfo::getReflection($class);
        }

        return null;
    }

    /**
     * @param \ReflectionClass|null $classReflection
     * @param string|null           $function
     *
     * @return null|\ReflectionFunctionAbstract
     */
    private static function extractBacktraceFuncReflection(\ReflectionClass $classReflection = null, string $function = null): ?\ReflectionFunctionAbstract
    {
        if (null !== $classReflection && $classReflection->hasMethod($function)) {
            return $classReflection->getMethod($function);
        }

        return null !== $function
            ? new \ReflectionFunction($function)
            : null;
    }
}
