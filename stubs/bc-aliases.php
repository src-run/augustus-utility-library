<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

class_alias(\SR\Utilities\Transform\NumberTransform::class, '\SR\Utils\Transform\NumberTransform');
class_alias(\SR\Utilities\Transform\StringTransform::class, '\SR\Utils\Transform\StringTransform');
class_alias(\SR\Utilities\Transform\TransformInterface::class, '\SR\Utils\Transform\TransformInterface');

class_alias(\SR\Utilities\ArrayInfo::class, '\SR\Utils\Info\ArrayInfo');
class_alias(\SR\Utilities\ArrayInfo::class, '\SR\Utils\Info\ClassInfo');
class_alias(\SR\Utilities\ArrayInfo::class, '\SR\Utils\Info\EngineInfo');
class_alias(\SR\Utilities\ArrayInfo::class, '\SR\Utils\Info\StringInfo');

class_alias(\SR\Utilities\Context\FileContext::class, '\SR\Utils\Context\FileContext');
class_alias(\SR\Utilities\Context\FileContextInterface::class, '\SR\Utils\Context\FileContextInterface');
