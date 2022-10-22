<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Utilities\IO\Buffered;

use SR\Utilities\Interpreter\Interpreter;
use SR\Utilities\IO\Buffered\Input\MemoryInputBuffered;
use SR\Utilities\IO\Buffered\Output\MemoryOutputBuffered;

trait MemoryBufferedTrait
{
    private ?string $mode = null;

    private ?float $memory = null;

    private ?string $scheme = null;

    private mixed $buffer = null;

    public function __destruct()
    {
        $this->close();
    }

    public function __toString(): string
    {
        return $this->get();
    }

    private function setup(): self
    {
        Interpreter::error();

        if (false !== ($this->buffer = @fopen($this->scheme(), $this->mode())) && !Interpreter::hasError()) {
            return $this;
        }

        throw new \RuntimeException(sprintf('Failed to open "%s" (mode: "%s"; limit: "%.02f megabytes / %d bytes"): %s', $this->scheme(), $this->mode(), $this->memory() ?? 'null', $this->memory() ? self::convertMegabytesToBytes($this->memory()) : 'null', Interpreter::error()->text()));
    }

    public function mode(): string
    {
        return $this->mode;
    }

    /**
     * @param string $mode Specify the file open mode to use for the buffer stream. Reference the `fopen` PHP manual
     *                     entry's "mode" argument documentation for acceptable values:
     *                     {@see http://php.net/manual/en/function.fopen.php#refsect1-function.fopen-parameters}.
     *
     * @return MemoryOutputBuffered|MemoryInputBuffered|MemoryBufferedTrait
     */
    public function setMode(string $mode): self
    {
        if ($this->isResourceOpen()) {
            throw new \RuntimeException(sprintf('Cannot set mode while resource is open. Close resource with %s::close().', static::class));
        }

        $this->mode = $mode;

        return $this;
    }

    public function memory(): ?float
    {
        return $this->memory;
    }

    /**
     * @param float|null $memory Optionally specify max memory to use before falling over to an on-disk, temporary
     *                           file. This value is passed in megabytes as a "float". A "null" value causes memory to
     *                           always be used, regardless of the buffer size, and a value of -1 causes falling
     *                           over to an on-disk temporary file at the default memory limitation, as defined by PHP.
     *
     * @return MemoryOutputBuffered|MemoryInputBuffered|MemoryBufferedTrait
     */
    public function setMemory(float $memory = null): self
    {
        if ($this->isResourceOpen()) {
            throw new \RuntimeException(sprintf('Cannot set memory while resource is open. Close resource with %s::close().', static::class));
        }

        $this->memory = $memory;

        return $this;
    }

    public function scheme(): string
    {
        if (null === $this->scheme) {
            $this->scheme = self::buildStreamScheme($this->memory());
        }

        return $this->scheme;
    }

    public function add(string $content, bool $newline = false): self
    {
        if (!$this->isResourceOpen()) {
            throw new \RuntimeException(sprintf('Failed to write "%s" data to closed buffer: re-open the buffer resource using the "%s::reset()" method.', mb_strlen($content) > 40 ? sprintf('%s [...]', mb_substr($content, 0, 40)) : $content, __CLASS__));
        }

        fwrite($this->buffer, $newline ? $content . PHP_EOL : $content);

        return $this;
    }

    public function get(int $length = null): string
    {
        if (!$this->isResourceOpen()) {
            throw new \RuntimeException(sprintf('Failed to read "%s" data from closed buffer: re-open the buffer resource using the "%s::reset()" method.', $length ? sprintf('%d bytes', $length) : 'all', __CLASS__));
        }

        rewind($this->buffer);

        return (null === $length ? stream_get_contents($this->buffer) : fread($this->buffer, $length)) ?: '';
    }

    public function reset(): self
    {
        $this->close();
        $this->setup();

        return $this;
    }

    public function close(): self
    {
        if ($this->isResourceOpen()) {
            @fclose($this->buffer);
            $this->scheme = null;
        }

        return $this;
    }

    /**
     * @return resource|null
     */
    public function resource(): mixed
    {
        return $this->buffer;
    }

    public function isResourceOpen(): bool
    {
        return is_resource($this->buffer);
    }

    private static function buildStreamScheme(?float $megabytes): string
    {
        if (null === $megabytes) {
            return 'php://memory';
        }

        if (0 > $megabytes) {
            return 'php://temp';
        }

        return sprintf('php://temp/maxmemory:%d', self::convertMegabytesToBytes($megabytes)) ?: 'php://temp';
    }

    private static function convertMegabytesToBytes(float $megabytes): int
    {
        return round($megabytes * 1024 * 1024, 0);
    }
}
