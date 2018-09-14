<?php

/*
 * This file is part of the `src-run/web-app-v1` project.
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

    /**
     * @return string
     */
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

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->bytes);
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator(
            array_combine($this->bytes(), $this->chars())
        );
    }

    /**
     * @param int $byte
     *
     * @return bool
     */
    public function isValidByte(int $byte): bool
    {
        return in_array($byte, $this->bytes, true) || ($byte >= 0 && $byte <= 255);
    }

    /**
     * @param string $char
     *
     * @return bool
     */
    public function isValidChar(string $char): bool
    {
        if (1 !== strlen($char)) {
            throw new \InvalidArgumentException(sprintf(
                'Provided value "%s" must be a single character (input length of %d provided).', $char, strlen($char)
            ));
        }

        return $this->isValidByte($this->charToByte($char));
    }

    /**
     * @param string $char
     *
     * @return int|null
     */
    public function charToByte(string $char): ?int
    {
        if (1 !== strlen($char)) {
            throw new \InvalidArgumentException(sprintf(
                'Provided value "%s" must be a single character.', $char
            ));
        }

        return $this->isValidByte($byte = ord($char)) ? $byte : null;
    }

    /**
     * @param int $byte
     *
     * @return string|null
     */
    public function byteToChar(int $byte): ?string
    {
        return $this->isValidByte($byte) ? chr($byte) : null;
    }

    /**
     * @return int
     */
    public function randomByte(): int
    {
        return $this->bytes[random_int(0, count($this->bytes) - 1)];
    }

    /**
     * @return string
     */
    public function randomChar(): string
    {
        return $this->byteToChar($this->randomByte());
    }

    /**
     * @param int $length
     *
     * @return CharactersGroup
     */
    public function randomGroup(int $length = 12): CharactersGroup
    {
        return $this->createCharacterGroup(array_map(function (): int {
            return $this->randomByte();
        }, 0 === $length ? [] : range(1, $length)));
    }

    /**
     * @param int $length
     *
     * @return string
     */
    public function randomString(int $length = 12): string
    {
        return $this->randomGroup($length)->__toString();
    }

    /**
     * @param array $array
     *
     * @return string
     */
    private static function arrayToString(array $array): string
    {
        return implode('', $array);
    }

    /**
     * @param array[] $byteSets
     *
     * @return CharactersGroup
     */
    private function createCharacterGroup(array ...$byteSets): CharactersGroup
    {
        return new CharactersGroup(...array_reduce($byteSets, function (array $all, $set): array {
            return array_merge($all, $set);
        }, []));
    }
}
