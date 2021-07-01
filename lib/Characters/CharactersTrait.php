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

trait CharactersTrait
{
    /**
     * @var int[]
     */
    private $bytes = [];

    public function __toString(): string
    {
        return self::arrayToString($this->chars());
    }

    /**
     * @return int[]
     */
    public function bytes(): array
    {
        return $this->bytes;
    }

    /**
     * @return string[]
     */
    public function chars(): array
    {
        return array_map(function (int $byte): string {
            return $this->byteToChar($byte);
        }, $this->bytes());
    }

    public function count(): int
    {
        return count($this->bytes);
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator(
            array_combine($this->bytes(), $this->chars())
        );
    }

    public function isValidByte(int $byte): bool
    {
        return in_array($byte, $this->bytes, true) || ($byte >= 0 && $byte <= 255);
    }

    public function isValidChar(string $char): bool
    {
        if (1 !== mb_strlen($char)) {
            throw new \InvalidArgumentException(sprintf('Provided value "%s" must be a single character (input length of %d provided).', $char, mb_strlen($char)));
        }

        return $this->isValidByte($this->charToByte($char));
    }

    public function charToByte(string $char): ?int
    {
        if (1 !== mb_strlen($char)) {
            throw new \InvalidArgumentException(sprintf('Provided value "%s" must be a single character.', $char));
        }

        return $this->isValidByte($byte = ord($char)) ? $byte : null;
    }

    public function byteToChar(int $byte): ?string
    {
        return $this->isValidByte($byte) ? chr($byte) : null;
    }

    public function randomByte(): int
    {
        return $this->bytes[random_int(0, count($this->bytes) - 1)];
    }

    public function randomChar(): string
    {
        return $this->byteToChar($this->randomByte());
    }

    public function randomGroup(int $length = 12): CharactersGroup
    {
        return $this->createCharacterGroup(array_map(function (): int {
            return $this->randomByte();
        }, 0 === $length ? [] : range(1, $length)));
    }

    public function randomString(int $length = 12): string
    {
        return $this->randomGroup($length)->__toString();
    }

    private static function arrayToString(array $array): string
    {
        return implode('', $array);
    }

    /**
     * @param array[] $byteSets
     */
    private function createCharacterGroup(array ...$byteSets): CharactersGroup
    {
        return new CharactersGroup(...array_reduce($byteSets, function (array $all, $set): array {
            return array_merge($all, $set);
        }, []));
    }
}
