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

interface OutputBufferedInterface
{
    /**
     * @return string
     */
    public function __toString(): string;

    /**
     * @param string $content
     * @param bool   $newline
     *
     * @return self
     */
    public function add(string $content, bool $newline = false);

    /**
     * @param int|null $length
     *
     * @return string
     */
    public function get(int $length = null): string;

    /**
     * @return self
     */
    public function reset();

    /**
     * @return self
     */
    public function close();

    /**
     * @return resource|null
     */
    public function getResource();

    /**
     * @return bool
     */
    public function isResourceOpen(): bool;

    /**
     * @return string
     */
    public function getScheme(): string;
}
