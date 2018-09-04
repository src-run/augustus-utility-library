<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Utilities\Interpreter\Reporting;

final class ReportingLevel
{
    /**
     * @var int[]
     */
    private $prior = [];

    /**
     * @var int
     */
    private $level;

    /**
     * @param int|null $level
     */
    public function __construct(int $level = null)
    {
        $this->level($level);
    }

    /**
     * @param int|null $level
     *
     * @return self
     */
    public static function create(int $level = null): self
    {
        return new self($level);
    }

    /**
     * @param int $position
     *
     * @return int|null
     */
    public function prior(int $position = 0): ?int
    {
        return $this->prior[count($this->prior) - 1 - $position] ?? null;
    }

    /**
     * @param int|null $level
     *
     * @return int
     */
    public function level(int $level = null): int
    {
        if (null === $level) {
            $this->level = error_reporting();
        }

        if (null !== $level) {
            $this->prior[] = error_reporting($this->level = $level);
        }

        return $this->level;
    }

    /**
     * @return self
     */
    public function revert(): self
    {
        if (0 < count($this->prior)) {
            $this->level = $prior = array_pop($this->prior);
            error_reporting($prior);
        }

        return $this;
    }
}
