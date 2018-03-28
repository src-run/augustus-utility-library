<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Output\Buffered;

final class MemoryOutputBuffered implements OutputBufferedInterface
{
    /**
     * @var string
     */
    private $scheme;

    /**
     * @var resource
     */
    private $buffer;

    /**
     * @param int $maximumMemoryUsage
     */
    public function __construct(int $maximumMemoryUsage = null)
    {
        $this->scheme = self::buildStreamScheme($maximumMemoryUsage);
        $this->reset();
    }

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
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * @param string $content
     * @param bool   $newline
     *
     * @return self
     */
    public function add(string $content, bool $newline = false): self
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
     * @return self
     */
    public function reset(): self
    {
        $this->close();
        $this->buffer = fopen($this->scheme, 'r+b');

        return $this;
    }

    /**
     * @return self
     */
    public function close(): self
    {
        if ($this->isResourceOpen()) {
            @fclose($this->buffer);
        }

        return $this;
    }

    /**
     * @return resource|null
     */
    public function getResource()
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
     * @param int|null $maxMemoryUsage
     *
     * @return string
     */
    private static function buildStreamScheme(int $maxMemoryUsage = null): string
    {
        return null === $maxMemoryUsage
            ? 'php://memory'
            : sprintf('php://temp/maxmemory:%d', $maxMemoryUsage * 1024 * 1024);
    }
}
