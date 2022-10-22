<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Utilities\Characters;

use SR\Utilities\Characters\Group\CharactersGroup;

class AsciiCharacters implements \Countable, \IteratorAggregate
{
    use CharactersTrait;

    /**
     * @var CharactersGroup[]
     */
    private static $sets;

    public function __construct()
    {
        $this->bytes = $this->mergedCharactersGroup(
            $this->numbers(), $this->letters(), $this->symbols()
        )->bytes();
    }

    public function numbers(): CharactersGroup
    {
        return $this->cachedCharactersGroup(__FUNCTION__, function (): array {
            return [range(48, 57)];
        });
    }

    public function letters(): CharactersGroup
    {
        return $this->cachedCharactersGroup(__FUNCTION__, function (): array {
            return [
                $this->lettersLower()->bytes(),
                $this->lettersUpper()->bytes(),
            ];
        });
    }

    public function lettersUpper(): CharactersGroup
    {
        return $this->cachedCharactersGroup(__FUNCTION__, function (): array {
            return [range(65, 90)];
        });
    }

    public function lettersLower(): CharactersGroup
    {
        return $this->cachedCharactersGroup(__FUNCTION__, function (): array {
            return [range(97, 122)];
        });
    }

    public function symbols(bool $readable = false): CharactersGroup
    {
        return $readable ? $this->symbolsSel() : $this->symbolsAll();
    }

    public function passwords(): CharactersGroup
    {
        return $this->cachedCharactersGroup(__FUNCTION__, function (): array {
            return [
                $this->numbers()->bytes(),
                $this->letters()->bytes(),
                $this->symbols(true)->bytes(),
            ];
        });
    }

    private function symbolsAll(): CharactersGroup
    {
        return $this->cachedCharactersGroup(__FUNCTION__, function (): array {
            return [
                range(32, 47),
                range(58, 64),
                range(91, 96),
                range(123, 126),
            ];
        });
    }

    private function symbolsSel(): CharactersGroup
    {
        return $this->cachedCharactersGroup(__FUNCTION__, function (): array {
            return [[
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
                123, // curly bracket (opening)
                124, // vertical bar
                125, // curly bracket (closing)
                126, // tilde
            ]];
        });
    }

    private function mergedCharactersGroup(CharactersGroup ...$groups): CharactersGroup
    {
        return $this->createCharacterGroup(...array_map(function (CharactersGroup $set): array {
            return $set->bytes();
        }, $groups));
    }

    private function cachedCharactersGroup(string $name, \Closure $provider): CharactersGroup
    {
        return isset(static::$sets[$name])
            ? static::$sets[$name]
            : static::$sets[$name] = $this->createCharacterGroup(...$provider());
    }
}
