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

use SR\Interpreter\Model\Error\ErrorModel;
use SR\Interpreter\Model\Error\ReportingModel;
use SR\Interpreter\Model\Error\Trace\BacktraceModel;

final class Interpreter
{
    /**
     * @param bool $clear
     *
     * @return ErrorModel
     */
    public static function error(bool $clear = true): ErrorModel
    {
        return ErrorModel::create($clear);
    }

    /**
     * @return bool
     */
    public static function hasError(): bool
    {
        return ErrorModel::create(false)->isReal();
    }

    /**
     * Clear the last error
     */
    public static function clearError(): ErrorModel
    {
        return ErrorModel::create(false)->clear();
    }

    /**
     * @param int $level
     *
     * @return BacktraceModel
     */
    public static function trace(int $level = 100): BacktraceModel
    {
        return BacktraceModel::create($level);
    }

    /**
     * @param int|null $level
     *
     * @return ReportingModel
     */
    public static function reporting(int $level = null): ReportingModel
    {
        return ReportingModel::create($level);
    }
}
