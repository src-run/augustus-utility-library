<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Utilities\Interpreter\Error;

use SR\Utilities\Interpreter\Backtrace\Backtrace;

final class Error
{
    /**
     * @var int
     */
    private $type;

    /**
     * @var string
     */
    private $text;

    /**
     * @var \SplFileInfo|null
     */
    private $file;

    /**
     * @var int|null
     */
    private $line;

    /**
     * @var bool
     */
    private $real;

    /**
     * @var Backtrace
     */
    private $debugBacktrace;

    /**
     * @var bool
     */
    private $clearLastOnDestruct;

    /**
     * @var bool
     */
    private $clearLastCompleted;

    /**
     * @param bool $clearLastOnDestruct
     * @param int  $debugBacktraceLimit
     */
    public function __construct(bool $clearLastOnDestruct = true, int $debugBacktraceLimit = 10)
    {
        [
            $this->type, $this->text, $this->file, $this->line, $this->debugBacktrace, $this->real
        ] = self::extractError($debugBacktraceLimit);

        $this->clearLastOnDestruct = $clearLastOnDestruct;
        $this->clearLastCompleted = false;
    }

    /**
     * Ensure we clear the last error once this object is destructed.
     */
    public function __destruct()
    {
        if (true === $this->clearLastOnDestruct) {
            $this->clear();
        }
    }

    /**
     * @param bool $clearLastOnDestruct
     * @param int  $debugBacktraceLimit
     *
     * @return self
     */
    public static function create(bool $clearLastOnDestruct = true, int $debugBacktraceLimit = 10): self
    {
        return new self($clearLastOnDestruct, $debugBacktraceLimit);
    }

    /**
     * @return int
     */
    public function type(): int
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function text(): string
    {
        return $this->text;
    }

    /**
     * @return null|\SplFileInfo
     */
    public function file(): ?\SplFileInfo
    {
        return $this->file;
    }

    /**
     * @return bool
     */
    public function hasFile(): bool
    {
        return null !== $this->file;
    }

    /**
     * @return int|null
     */
    public function line(): ?int
    {
        return $this->line;
    }

    /**
     * @return null|Backtrace
     */
    public function trace(): ?Backtrace
    {
        return $this->debugBacktrace;
    }

    /**
     * @return bool
     */
    public function hasTrace(): bool
    {
        return null !== $this->debugBacktrace;
    }

    /**
     * @return bool
     */
    public function isReal(): bool
    {
        return true === $this->real;
    }

    /**
     * @return bool
     */
    public function isMock(): bool
    {
        return true !== $this->real;
    }

    /**
     * @param bool $force
     *
     * @return self
     */
    public function clear(bool $force = false): self
    {
        if ((true !== $this->clearLastCompleted && true === $this->isEquitableToLast()) || true === $force) {
            @error_clear_last();
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isEquitableToLast(): bool
    {
        [$type, $text, $file, $line, $trace, $real] = self::extractError();

        return $this->isReal() === $real
            && $this->type() === $type
            && $this->text() === $text
            && $this->line() === $line
            && (string) $this->file() === (string) $file;
    }

    /**
     * @param int|null $debugBacktraceLimit
     *
     * @return array
     */
    private static function extractError(int $debugBacktraceLimit = null): array
    {
        $data = error_get_last() ?? [];

        return [
            self::extractErrorType($data),
            self::extractErrorText($data),
            self::extractErrorFile($data),
            self::extractErrorLine($data),
            0 < $debugBacktraceLimit ? Backtrace::create($debugBacktraceLimit) : null,
            0 < count($data),
        ];
    }

    /**
     * @param array $data
     *
     * @return int
     */
    private static function extractErrorType(array $data): int
    {
        return $data['type'] ?? -1000;
    }

    /**
     * @param array $data
     *
     * @return string
     */
    private static function extractErrorText(array $data): string
    {
        return $data['message'] ?? 'No internal interpreter error occurred.';
    }

    /**
     * @param array $error
     *
     * @return null|\SplFileInfo
     */
    private static function extractErrorFile(array $error): ?\SplFileInfo
    {
        return is_file($file = $error['file'] ?? null) ? new \SplFileInfo($file) : null;
    }

    /**
     * @param array $data
     *
     * @return int
     */
    private static function extractErrorLine(array $data): int
    {
        return $data['line'] ?? 0;
    }
}
