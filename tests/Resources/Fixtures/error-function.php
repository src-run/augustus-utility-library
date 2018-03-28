<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

function get_interpreter_error(bool $clear = true): \SR\Interpreter\Model\Error\ErrorModel
{
    return \SR\Interpreter\Interpreter::error($clear);
}
