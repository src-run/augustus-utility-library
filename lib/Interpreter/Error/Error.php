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

    public static function create(bool $clearLastOnDestruct = true, int $debugBacktraceLimit = 10): self
    {
        return new self($clearLastOnDestruct, $debugBacktraceLimit);
    }

    public function type(): int
    {
        return $this->type;
    }

    public function text(): string
    {
        return $this->text;
    }

    public function file(): ?\SplFileInfo
    {
        return $this->file;
    }

    public function hasFile(): bool
    {
        return null !== $this->file;
    }

    public function line(): ?int
    {
        return $this->line;
    }

    public function trace(): ?Backtrace
    {
        return $this->debugBacktrace;
    }

    public function hasTrace(): bool
    {
        return null !== $this->debugBacktrace;
    }

    public function isReal(): bool
    {
        return true === $this->real;
    }

    public function isMock(): bool
    {
        return true !== $this->real;
    }

    public function clear(bool $force = false): self
    {
        if ((true !== $this->clearLastCompleted && true === $this->isEquitableToLast()) || true === $force) {
            @error_clear_last();
        }

        return $this;
    }

    public function isEquitableToLast(): bool
    {
        [$type, $text, $file, $line, $trace, $real] = self::extractError();

        return $this->isReal() === $real
            && $this->type() === $type
            && $this->text() === $text
            && $this->line() === $line
            && (string) $this->file() === (string) $file;
    }

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

    private static function extractErrorType(array $data): int
    {
        return $data['type'] ?? -1000;
    }

    private static function extractErrorText(array $data): string
    {
        return $data['message'] ?? 'No internal interpreter error occurred.';
    }

    private static function extractErrorFile(array $error): ?\SplFileInfo
    {
        return is_file($file = $error['file'] ?? null) ? new \SplFileInfo($file) : null;
    }

    private static function extractErrorLine(array $data): int
    {
        return $data['line'] ?? 0;
    }
}
