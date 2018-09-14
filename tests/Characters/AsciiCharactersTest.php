<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Tests\Utilities\Context;

use PHPUnit\Framework\TestCase;
use SR\Utilities\Characters\AsciiCharacters;
use SR\Utilities\Characters\Group\CharactersGroup;

/**
 * @covers \SR\Utilities\Characters\Group\CharactersGroup
 * @covers \SR\Utilities\Characters\AsciiCharacters
 * @covers \SR\Utilities\Characters\CharactersTrait
 */
class AsciiCharactersTest extends TestCase
{
    public function testIsValidCharException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new AsciiCharacters())->isValidChar('aa');
    }

    public function testCharToByteException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new AsciiCharacters())->charToByte('aa');
    }

    public function testAsciiCharacters(): void
    {
        $c = new AsciiCharacters();

        $this->assertInstanceOf(CharactersGroup::class, $n = $c->numbers());
        $this->assertInstanceOf(CharactersGroup::class, $l = $c->letters());
        $this->assertInstanceOf(CharactersGroup::class, $u = $c->lettersUpper());
        $this->assertInstanceOf(CharactersGroup::class, $w = $c->lettersLower());
        $this->assertInstanceOf(CharactersGroup::class, $s = $c->symbols());
        $this->assertInstanceOf(CharactersGroup::class, $r = $c->symbols(true));
        $this->assertInstanceOf(CharactersGroup::class, $p = $c->passwords());
    }

    /**
     * @return \Generator|array
     */
    public static function provideNumbersGroupData(): \Generator
    {
        foreach (self::getNumbers() as $bytes) {
            yield [$bytes, chr($bytes)];
        }
    }

    /**
     * @dataProvider provideNumbersGroupData
     *
     * @param int    $byte
     * @param string $char
     */
    public function testNumbersGroupEach(int $byte, string $char): void
    {
        $this->doTestGroupEach(
            (new AsciiCharacters())->numbers(),
            $byte,
            $char,
            self::getNumbers(),
            self::getNumbers(true)
        );
    }

    public function testNumbersGroup(): void
    {
        $this->doTestGroup(
            $g = (new AsciiCharacters())->numbers(),
            $b = self::getNumbers(),
            $c = self::getNumbers(true)
        );
        $this->doTestGroupRandom(
            $g,
            $b,
            $c
        );
    }

    /**
     * @return \Generator|array
     */
    public static function provideLettersGroupData(): \Generator
    {
        foreach (self::getLetters() as $bytes) {
            yield [$bytes, chr($bytes)];
        }
    }

    /**
     * @dataProvider provideLettersGroupData
     *
     * @param int    $byte
     * @param string $char
     */
    public function testLettersGroupEach(int $byte, string $char): void
    {
        $this->doTestGroupEach(
            (new AsciiCharacters())->letters(),
            $byte,
            $char,
            self::getLetters(),
            self::getLetters(true)
        );
    }

    public function testLettersGroup(): void
    {
        $this->doTestGroup(
            $g = (new AsciiCharacters())->letters(),
            $b = self::getLetters(),
            $c = self::getLetters(true)
        );
        $this->doTestGroupRandom(
            $g,
            $b,
            $c
        );
    }

    /**
     * @return \Generator|array
     */
    public static function provideLettersUpperGroupData(): \Generator
    {
        foreach (self::getLettersUpper() as $bytes) {
            yield [$bytes, chr($bytes)];
        }
    }

    /**
     * @dataProvider provideLettersUpperGroupData
     *
     * @param int    $byte
     * @param string $char
     */
    public function testLettersUpperGroupEach(int $byte, string $char): void
    {
        $this->doTestGroupEach(
            (new AsciiCharacters())->lettersUpper(),
            $byte,
            $char,
            self::getLettersUpper(),
            self::getLettersUpper(true)
        );
    }

    public function testLettersUpperGroup(): void
    {
        $this->doTestGroup(
            $g = (new AsciiCharacters())->lettersUpper(),
            $b = self::getLettersUpper(),
            $c = self::getLettersUpper(true)
        );
        $this->doTestGroupRandom(
            $g,
            $b,
            $c
        );
    }

    /**
     * @return \Generator|array
     */
    public static function provideLettersLowerGroupData(): \Generator
    {
        foreach (self::getLettersLower() as $bytes) {
            yield [$bytes, chr($bytes)];
        }
    }

    /**
     * @dataProvider provideLettersLowerGroupData
     *
     * @param int    $byte
     * @param string $char
     */
    public function testLettersLowerGroupEach(int $byte, string $char): void
    {
        $this->doTestGroupEach(
            (new AsciiCharacters())->lettersLower(),
            $byte,
            $char,
            self::getLettersLower(),
            self::getLettersLower(true)
        );
    }

    public function testLettersLowerGroup(): void
    {
        $this->doTestGroup(
            $g = (new AsciiCharacters())->lettersLower(),
            $b = self::getLettersLower(),
            $c = self::getLettersLower(true)
        );
        $this->doTestGroupRandom(
            $g,
            $b,
            $c
        );
    }

    /**
     * @return \Generator|array
     */
    public static function provideSymbolsAllGroupData(): \Generator
    {
        foreach (self::getSymbolsAll() as $bytes) {
            yield [$bytes, chr($bytes)];
        }
    }

    /**
     * @dataProvider provideSymbolsAllGroupData
     *
     * @param int    $byte
     * @param string $char
     */
    public function testSymbolsAllGroupEach(int $byte, string $char): void
    {
        $this->doTestGroupEach(
            (new AsciiCharacters())->symbols(),
            $byte,
            $char,
            self::getSymbolsAll(),
            self::getSymbolsAll(true)
        );
    }

    public function testSymbolsAllGroup(): void
    {
        $this->doTestGroup(
            $g = (new AsciiCharacters())->symbols(),
            $b = self::getSymbolsAll(),
            $c = self::getSymbolsAll(true)
        );
        $this->doTestGroupRandom(
            $g,
            $b,
            $c
        );
    }

    /**
     * @return \Generator|array
     */
    public static function provideSymbolsSelGroupData(): \Generator
    {
        foreach (self::getSymbolsSel() as $bytes) {
            yield [$bytes, chr($bytes)];
        }
    }

    /**
     * @dataProvider provideSymbolsSelGroupData
     *
     * @param int    $byte
     * @param string $char
     */
    public function testSymbolsSelGroupEach(int $byte, string $char): void
    {
        $this->doTestGroupEach(
            (new AsciiCharacters())->symbols(true),
            $byte,
            $char,
            self::getSymbolsSel(),
            self::getSymbolsSel(true)
        );
    }

    public function testSymbolsSelGroup(): void
    {
        $this->doTestGroup(
            $g = (new AsciiCharacters())->symbols(true),
            $b = self::getSymbolsSel(),
            $c = self::getSymbolsSel(true)
        );
        $this->doTestGroupRandom(
            $g,
            $b,
            $c
        );
    }

    /**
     * @return \Generator|array
     */
    public static function providePasswordGroupData(): \Generator
    {
        foreach (self::getPassword() as $bytes) {
            yield [$bytes, chr($bytes)];
        }
    }

    /**
     * @dataProvider providePasswordGroupData
     *
     * @param int    $byte
     * @param string $char
     */
    public function testPasswordGroupEach(int $byte, string $char): void
    {
        $this->doTestGroupEach(
            (new AsciiCharacters())->passwords(),
            $byte,
            $char,
            self::getPassword(),
            self::getPassword(true)
        );
    }

    public function testPasswordGroup(): void
    {
        $this->doTestGroup(
            $g = (new AsciiCharacters())->passwords(),
            $b = self::getPassword(),
            $c = self::getPassword(true)
        );
        $this->doTestGroupRandom(
            $g,
            $b,
            $c
        );
    }

    /**
     * @param CharactersGroup $group
     * @param int             $b
     * @param string          $c
     * @param int[]           $bytes
     * @param string[]        $chars
     */
    private function doTestGroupEach($group, int $b, string $c, array $bytes, array $chars): void
    {
        $this->assertInstanceOf(CharactersGroup::class, $group);
        $this->assertTrue($group->isValidByte($b));
        $this->assertTrue($group->isValidChar($c));
        $this->assertSame($b, $group->charToByte($c));
        $this->assertSame($c, $group->byteToChar($b));
        $this->assertContains($b, $group->bytes());
        $this->assertContains($c, $group->chars());

        $this->assertContains($group->randomChar(), $chars);
        $this->assertContains($group->randomByte(), $bytes);

        for ($i = 0; $i < 25; $i++) {
            $random = $group->randomGroup($i);
            $this->assertInstanceOf(CharactersGroup::class, $random);
            $this->assertCount($i, $random);
        }
    }

    /**
     * @param CharactersGroup $group
     * @param int[]           $bytes
     * @param string[]        $chars
     */
    private function doTestGroup(CharactersGroup $group, array $bytes, array $chars): void
    {
        $this->assertCount(count($bytes), $group);

        foreach ($group as $b => $c) {
            $this->assertContains($b, $bytes);
            $this->assertContains($c, $chars);
        }
    }

    /**
     * @param CharactersGroup $group
     * @param int[]           $bytes
     * @param string[]        $chars
     */
    private function doTestGroupRandom(CharactersGroup $group, array $bytes, array $chars): void
    {
        for ($i = 0; $i < 10; $i++) {
            $random = $group->randomGroup($i);
            $this->assertInstanceOf(CharactersGroup::class, $random);
            $this->assertCount($i, $random);

            foreach ($random as $b => $c) {
                $this->doTestGroupEach($random, $b, $c, $bytes, $chars);
            }

            $string = $group->randomString($i);
            $this->assertSame($i, mb_strlen($string));

            if (0 === $i) {
                continue;
            }

            foreach (str_split($string) as $c) {
                $this->assertTrue($group->isValidChar($c));
                $this->assertContains($c, $chars);
                $this->assertContains($group->charToByte($c), $bytes);
            }
        }
    }

    /**
     * @param bool $chars
     *
     * @return int[]
     */
    private static function getNumbers(bool $chars = false): array
    {
        $bytes = range(48, 57);

        return $chars ? self::mapBytesToChars($bytes) : $bytes;
    }

    /**
     * @param bool $chars
     *
     * @return int[]
     */
    private static function getLetters(bool $chars = false): array
    {
        $bytes = array_merge(
            self::getLettersUpper(),
            self::getLettersLower()
        );

        return $chars ? self::mapBytesToChars($bytes) : $bytes;
    }

    /**
     * @param bool $chars
     *
     * @return int[]
     */
    private static function getLettersUpper(bool $chars = false): array
    {
        $bytes = range(65, 90);

        return $chars ? self::mapBytesToChars($bytes) : $bytes;
    }

    /**
     * @param bool $chars
     *
     * @return int[]
     */
    private static function getLettersLower(bool $chars = false): array
    {
        $bytes = range(97, 122);

        return $chars ? self::mapBytesToChars($bytes) : $bytes;
    }

    /**
     * @param bool $chars
     *
     * @return int[]
     */
    private static function getSymbolsAll(bool $chars = false): array
    {
        $bytes = array_merge(
            range(32, 47),
            range(58, 64),
            range(91, 96),
            range(123, 126)
        );

        return $chars ? self::mapBytesToChars($bytes) : $bytes;
    }

    /**
     * @param bool $chars
     *
     * @return int[]
     */
    private static function getSymbolsSel(bool $chars = false): array
    {
        $bytes = [
            33, // exclamation mark
            35, // number sign
            36, // dollar sign
            37, // percent sign
            38, // ampersand
            40, // parentheses (opening)
            41, // parentheses (closing)
            42, // asterisk
            43, // plus sign
            45, // hyphen-minus
            58, // colon
            59, // semicolon
            61, // equal sign
            63, // question mark
            64, // at sign
            91, // square bracket (opening)
            93, // square bracket (closing)
            94, // circumflex accent
            95, // underscore
            123,// curly bracket (opening)
            124,// vertical bar
            125,// curly bracket (closing)
            126 // tilde
        ];

        return $chars ? self::mapBytesToChars($bytes) : $bytes;
    }

    /**
     * @param bool $chars
     *
     * @return int[]
     */
    private static function getPassword(bool $chars = false): array
    {
        $bytes = array_merge(
            self::getNumbers(),
            self::getLetters(),
            self::getSymbolsSel()
        );

        return $chars ? self::mapBytesToChars($bytes) : $bytes;
    }

    /**
     * @param int[] $bytes
     *
     * @return string[]
     */
    private static function mapBytesToChars(array $bytes): array
    {
        return array_map(function (int $byte): string {
            return chr($byte);
        }, $bytes);
    }
}
