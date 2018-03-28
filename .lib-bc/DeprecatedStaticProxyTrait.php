<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Util;

trait DeprecatedStaticProxyTrait
{
    /**
     * @param string  $name
     * @param mixed[] $arguments
     *
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        @trigger_error(sprintf(
            'Calling "%s" is deprecated and has been replaced with "%s".', get_called_class(), static::REAL_CLASS
        ), E_USER_DEPRECATED);

        return call_user_func_array(sprintf('%s::%s', static::REAL_CLASS, $name), $arguments);
    }
}
