<?php

/**
 * @param bool $clear
 *
 * @return \SR\Interpreter\Model\Error\ErrorModel
 */
function get_interpreter_error(bool $clear = true): \SR\Interpreter\Model\Error\ErrorModel
{
    return \SR\Interpreter\Interpreter::error($clear);
}
