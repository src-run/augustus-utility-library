<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Utilities\Test;

use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class BcAliasTest extends TestCase
{
    public static function provideAliasesData(): array
    {
        return [
            [\SR\Utilities\Transform\NumberTransform::class, '\SR\Utils\Transform\NumberTransform'],
            [\SR\Utilities\Transform\StringTransform::class, '\SR\Utils\Transform\StringTransform'],
            [\SR\Utilities\Transform\TransformInterface::class, '\SR\Utils\Transform\TransformInterface'],
            [\SR\Utilities\ArrayInfo::class, '\SR\Utils\Info\ArrayInfo'],
            [\SR\Utilities\ArrayInfo::class, '\SR\Utils\Info\ClassInfo'],
            [\SR\Utilities\ArrayInfo::class, '\SR\Utils\Info\EngineInfo'],
            [\SR\Utilities\ArrayInfo::class, '\SR\Utils\Info\StringInfo'],
            [\SR\Utilities\Context\FileContext::class, '\SR\Utils\Context\FileContext'],
            [\SR\Utilities\Context\FileContextInterface::class, '\SR\Utils\Context\FileContextInterface'],
        ];
    }

    /**
     * @dataProvider provideAliasesData
     *
     * @param string $new
     * @param string $old
     */
    public function testAliases(string $new, string $old): void
    {
        try {
            $this->assertSame($new, (new \ReflectionClass($old))->getName());
        } catch (\ReflectionException $e) {
            $this->fail($e->getMessage());
        }
    }
}
