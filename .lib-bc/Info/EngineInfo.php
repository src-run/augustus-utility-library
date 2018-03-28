<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Util\Info;

use SR\Util\DeprecatedStaticProxyTrait;

/**
 * @deprecated Use {@see \SR\Utilities\EngineInfo} instead.
 */
class EngineInfo
{
    use DeprecatedStaticProxyTrait;

    /**
     * @var string
     */
    public const REAL_CLASS = \SR\Utilities\EngineInfo::class;
}
