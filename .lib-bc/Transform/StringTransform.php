<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Util\Transform;

/**
 * @deprecated Use {@see \SR\Utilities\Transform\StringTransform} instead.
 */
class StringTransform extends \SR\Utilities\Transform\StringTransform
{
    /**
     * @var string
     */
    public const REAL_CLASS = \SR\Utilities\Transform\StringTransform::class;

    /**
     * @deprecated Use {@see \SR\Utilities\Transform\StringTransform::__construct()} instead.
     *
     * @param null|string $string
     * @param bool        $mutable
     */
    public function __construct($string = null, bool $mutable = false)
    {
        @trigger_error(sprintf(
            'Calling "%s" is deprecated and has been replaced with "%s".', get_called_class(), static::REAL_CLASS
        ), E_USER_DEPRECATED);

        parent::__construct($string, $mutable);
    }
}