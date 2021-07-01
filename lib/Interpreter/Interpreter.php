<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Utilities\Interpreter;

use SR\Utilities\Interpreter\Backtrace\Backtrace;
use SR\Utilities\Interpreter\Error\Error;
use SR\Utilities\Interpreter\Reporting\ReportingLevel;

final class Interpreter
{
    public static function error(bool $clear = true): Error
    {
        return Error::create($clear);
    }

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

    public static function trace(int $level = 100): Backtrace
    {
        return Backtrace::create($level);
    }

    public static function reporting(int $level = null): ReportingLevel
    {
        return ReportingLevel::create($level);
    }
}
