<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Util\Context;

/**
 * @deprecated Use {@see \SR\Utilities\Context\FileContext} instead.
 */
class FileContext extends \SR\Utilities\Context\FileContext implements FileContextInterface
{
    /**
     * @var string
     */
    public const REAL_CLASS = \SR\Utilities\Context\FileContext::class;

    /**
     * @deprecated Use {@see \SR\Utilities\Context\FileContext::__construct()} instead.
     *
     * @param string $file
     * @param int    $line
     */
    public function __construct(string $file, int $line)
    {
        @trigger_error(sprintf(
            'Calling "%s" is deprecated and has been replaced with "%s".', get_called_class(), static::REAL_CLASS
        ), E_USER_DEPRECATED);

        parent::__construct($file, $line);
    }
}
