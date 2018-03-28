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
use SR\Util\Info\ArrayInfo;

/**
 * @coversNothing
 */
class BcAliasTest extends TestCase
{
    /**
     * @group legacy
     *
     * @return array
     */
    public static function provideAliasesData(): array
    {
        return [
            [\SR\Utilities\Transform\NumberTransform::class, '\SR\Util\Transform\NumberTransform'],
            [\SR\Utilities\Transform\StringTransform::class, '\SR\Util\Transform\StringTransform'],
            [\SR\Utilities\Transform\TransformInterface::class, '\SR\Util\Transform\TransformInterface'],
            [\SR\Utilities\ArrayQuery::class, '\SR\Util\Info\ArrayInfo'],
            [\SR\Utilities\ClassQuery::class, '\SR\Util\Info\ClassInfo'],
            [\SR\Utilities\EngineQuery::class, '\SR\Util\Info\EngineInfo'],
            [\SR\Utilities\StringQuery::class, '\SR\Util\Info\StringInfo'],
            [\SR\Utilities\Context\FileContext::class, '\SR\Util\Context\FileContext'],
            [\SR\Utilities\Context\FileContextInterface::class, '\SR\Util\Context\FileContextInterface'],
        ];
    }

    /**
     * @group legacy
     *
     * @dataProvider provideAliasesData
     *
     * @param string $new
     * @param string $old
     */
    public function testAliases(string $new, string $old): void
    {
        if (false !== strpos($old, 'Interface')) {
            try {
                $this->assertTrue((new \ReflectionClass($old))->isSubclassOf($new));
            } catch (\ReflectionException $e) {
                $this->fail($e->getMessage());
            }
        } else {
            try {
                $this->assertTrue(
                    (new \ReflectionClass($old))->isSubclassOf($new) ||
                    false !== strpos(constant(sprintf('%s::REAL_CLASS', $old)), $new)
                );
            } catch (\ReflectionException $e) {
                $this->fail($e->getMessage());
            }
        }
    }

    /**
     * @group legacy
     *
     * @expectedDeprecation Calling "SR\Util\Info\ArrayInfo" is deprecated and has been replaced with "SR\Utilities\ArrayQuery".
     */
    public function testStaticDeprecationMessage()
    {
        ArrayInfo::isAssociative([]);
    }
}
