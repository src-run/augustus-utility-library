<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Interpreter;

use SR\Interpreter\Backtrace\Backtrace;
use SR\Interpreter\Error\Error;
use SR\Interpreter\Reporting\ReportingLevel;

final class Interpreter
{
    /**
     * @param bool $clear
     *
     * @return Error
     */
    public static function error(bool $clear = true): Error
    {
        return Error::create($clear);
    }

    /**
     * @return bool
     */
    public static function hasError(): bool
    {
        return Error::create(false)->isReal();
    }

    /**
     * Clear the last error
     */
    public static function clearError(): Error
    {
        return Error::create(false)->clear();
    }

    /**
     * @param int $level
     *
     * @return Backtrace
     */
    public static function trace(int $level = 100): Backtrace
    {
        return Backtrace::create($level);
    }

    /**
     * @param int|null $level
     *
     * @return \SR\Interpreter\Reporting\ReportingLevel
     */
    public static function reporting(int $level = null): ReportingLevel
    {
        return ReportingLevel::create($level);
    }
}
