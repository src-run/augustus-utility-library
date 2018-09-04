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

trait MemoryBufferedTrait
{
    /**
     * @var string
     */
    private $mode;

    /**
     * @var float|null
     */
    private $memory;

    /**
     * @var string
     */
    private $scheme;

    /**
     * @var resource
     */
    private $buffer;

    /**
     * Ensure the file resource is closed.
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->get();
    }

    /**
     * @return string
     */
    public function mode(): string
    {
        return $this->mode;
    }

    /**
     * @param string $mode Specify the file open mode to use for the buffer stream. Reference the `fopen` PHP manual
     *                     entry's "mode" argument documentation for acceptable values:
     *                     {@see http://php.net/manual/en/function.fopen.php#refsect1-function.fopen-parameters}.
     *
     * @throws \RuntimeException Throws when setter method is called prior to the buffer being closed.
     *
     * @return BufferedInterface|MemoryBufferedTrait
     */
    public function setMode(string $mode): BufferedInterface
    {
        if ($this->isResourceOpen()) {
            throw new \RuntimeException(sprintf(
                'Cannot set mode while resource is open. Close resource with %s::close().', get_called_class()
            ));
        }

        $this->mode = $mode;

        return $this;
    }

    /**
     * @return float|null
     */
    public function memory(): ?float
    {
        return $this->memory;
    }

    /**
     * @param float|null $memory Optionally specify max memory to use before falling over to an on-disk, temporary
     *                           file. This value is passed in megabytes as a "float". A "null" value causes memory to
     *                           always be used, irregardless of the buffer size, and a value of -1 causes falling
     *                           over to an on-disk temporary file at the default memory limitation, as defined by PHP.
     *
     * @throws \RuntimeException Throws when setter method is called prior to the buffer being closed.
     *
     * @return BufferedInterface|MemoryBufferedTrait
     */
    public function setMemory(float $memory = null): BufferedInterface
    {
        if ($this->isResourceOpen()) {
            throw new \RuntimeException(sprintf(
                'Cannot set memory while resource is open. Close resource with %s::close().', get_called_class()
            ));
        }

        $this->memory = $memory;

        return $this;
    }

    /**
     * @return string
     */
    public function scheme(): string
    {
        if (null === $this->scheme) {
            $this->scheme = self::buildStreamScheme($this->memory());
        }

        return $this->scheme;
    }

    /**
     * @param string $content
     * @param bool   $newline
     *
     * @return BufferedInterface|MemoryBufferedTrait
     */
    public function add(string $content, bool $newline = false): BufferedInterface
    {
        if (!$this->isResourceOpen()) {
            throw new \RuntimeException(sprintf(
                'Failed to write "%s" data to closed buffer: re-open the buffer resource using the "%s::reset()" method.',
                mb_strlen($content) > 40 ? sprintf('%s [...]', mb_substr($content, 0, 40)) : $content, __CLASS__
            ));
        }

        fwrite($this->buffer, $newline ? $content.PHP_EOL : $content);

        return $this;
    }

    /**
     * @param int|null $length
     *
     * @return string
     */
    public function get(int $length = null): string
    {
        if (!$this->isResourceOpen()) {
            throw new \RuntimeException(sprintf(
                'Failed to read "%s" data from closed buffer: re-open the buffer resource using the "%s::reset()" method.',
                $length ? sprintf('%d bytes', $length) : 'all', __CLASS__
            ));
        }

        rewind($this->buffer);

        return (null === $length ? stream_get_contents($this->buffer) : fread($this->buffer, $length)) ?: '';
    }

    /**
     * @throws \RuntimeException Throws on failure to re-setup buffer during reset operation.
     *
     * @return BufferedInterface|MemoryBufferedTrait
     */
    public function reset(): BufferedInterface
    {
        $this->close();
        $this->setup();

        return $this;
    }

    /**
     * @return BufferedInterface|MemoryBufferedTrait
     */
    public function close(): BufferedInterface
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
    public function resource()
    {
        return $this->buffer;
    }

    /**
     * @return bool
     */
    public function isResourceOpen(): bool
    {
        return is_resource($this->buffer);
    }

    /**
     * @throws \RuntimeException Will throw on failure to open stream using `fopen`.
     *
     * @return BufferedInterface|MemoryBufferedTrait
     */
    private function setup(): BufferedInterface
    {
        Interpreter::error();

        if (false !== ($this->buffer = @fopen($this->scheme(), $this->mode())) && !Interpreter::hasError()) {
            return $this;
        }

        throw new \RuntimeException(vsprintf('Failed to open "%s" (mode: "%s"; limit: "%.02f megabytes / %d bytes"): %s', [
            $this->scheme(),
            $this->mode(),
            $this->memory() ?? 'null',
            $this->memory() ? self::convertMegabytesToBytes($this->memory()) : 'null',
            Interpreter::error()->text(),
        ]));
    }

    /**
     * @param float|null $megabytes
     *
     * @return string
     */
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

    /**
     * @param float $megabytes
     *
     * @return int
     */
    private static function convertMegabytesToBytes(float $megabytes): int
    {
        return round($megabytes * 1024 * 1024, 0);
    }
}
