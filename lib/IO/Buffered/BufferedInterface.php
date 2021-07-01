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

interface BufferedInterface
{
    public function __toString(): string;

    /**
     * @return self
     */
    public function add(string $content, bool $newline = false);

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
    public function resource();

    public function isResourceOpen(): bool;

    public function scheme(): string;
}
