<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Utilities\Transform;

use SR\Utilities\Transform\Argument\Expression\Archetype\RangedArchetype;
use SR\Utilities\Transform\Argument\Expression\Archetype\StringArchetype;
use SR\Utilities\Transform\Argument\Expression\Representative\SearchReplaceRepresentative;

class StringTransform extends AbstractTransform
{
    public function set(mixed $value): self
    {
        if (false === static::isConsumable($value)) {
            throw new \InvalidArgumentException('Value is not a string and could not be coerced to one.');
        }

        return parent::set((string) $value);
    }

    /**
     * Perform string replacement using regular expression and replacement. Optionally turn regex inverse (negative)
     * and enforce case sensitivity.
     */
    public function replace(SearchReplaceRepresentative $config): self
    {
        return $this->apply(function () use ($config) {
            return preg_replace($config->regex(), $config->replacement(), $this->get());
        });
    }

    /**
     * Perform conversion to alpha characters only.
     */
    public function toAlpha(): self
    {
        return $this->replace(new SearchReplaceRepresentative('', new RangedArchetype('a-z', true)));
    }

    /**
     * Perform conversion to numeric characters only.
     */
    public function toNumeric(): self
    {
        return $this->replace(new SearchReplaceRepresentative('', new RangedArchetype('0-9', true)));
    }

    /**
     * Perform conversion to alphanumeric characters only.
     */
    public function toAlphanumeric(): self
    {
        return $this->replace(new SearchReplaceRepresentative('', new RangedArchetype('a-z0-9', true)));
    }

    /**
     * Perform conversion to alphanumeric and dash characters only.
     */
    public function toAlphanumericAndDashes(): self
    {
        return $this->replace(new SearchReplaceRepresentative('', new RangedArchetype('a-z0-9-', true)));
    }

    public function spacesToDashes(): self
    {
        return $this->replace(new SearchReplaceRepresentative('-', new StringArchetype('[ ]+')));
    }

    /**
     * Perform conversion to alphanumeric and spaces to dashes only.
     */
    public function toAlphanumericAndSpacesToDashes(): self
    {
        return $this
            ->replace(new SearchReplaceRepresentative('-', new StringArchetype('[\s\n]+')))
            ->toAlphanumericAndDashes()
            ->replace(new SearchReplaceRepresentative('-', new StringArchetype('[-]+')))
        ;
    }

    public function dashesToSpaces(): self
    {
        return $this->replace(new SearchReplaceRepresentative(' ', new StringArchetype('[-]+')));
    }

    public function slugify(bool $lowercase = true): self
    {
        return $this->apply(function (self $value) {
            $result = $value
                ->setMutable(true)
                ->replace(new SearchReplaceRepresentative('-', new RangedArchetype('a-z0-9-', true)))
                ->replace(new SearchReplaceRepresentative('-', new StringArchetype('[-]+')))
                ->replace((new SearchReplaceRepresentative('', new StringArchetype('[-]')))->enableAnchorLeft())
                ->replace((new SearchReplaceRepresentative('', new StringArchetype('[-]')))->enableAnchorRight())
                ->toLower()
            ;

            return $this->returnInstance($result->get());
        });
    }

    public function toLower(): self
    {
        return $this->apply(function () {
            return mb_strtolower($this->get());
        });
    }

    public function toUpper(): self
    {
        return $this->apply(function () {
            return mb_strtoupper($this->get());
        });
    }

    public function camelToPascalCase(): self
    {
        return $this->apply(function () {
            return ucfirst($this->get());
        });
    }

    public function camelToSnakeCase(): self
    {
        return $this->apply(function () {
            return preg_replace('#(?<=\\w)(?=[A-Z])#', '_$1', $this->get());
        })->toLower();
    }

    public function camelToSpinalCase(): self
    {
        return $this->apply(function () {
            return preg_replace('#(?<=\\w)(?=[A-Z])#', '-$1', $this->get());
        })->toLower();
    }

    public function pascalToCamelCase(): self
    {
        return $this->apply(function () {
            return lcfirst($this->get());
        });
    }

    public function pascalToSnakeCase(): self
    {
        return $this->apply(function () {
            return $this->camelToSnakeCase();
        });
    }

    public function pascalToSpinalCase(): self
    {
        return $this->apply(function () {
            return $this->camelToSpinalCase();
        });
    }

    public function snakeToCamelCase(): self
    {
        return $this->apply(function () {
            return preg_replace_callback('{(_)([a-z])}', function ($match) {
                return mb_strtoupper($match[2]);
            }, $this->get());
        });
    }

    public function snakeToPascalCase(): self
    {
        return $this->apply(function () {
            return $this->snakeToCamelCase()->camelToPascalCase();
        });
    }

    public function snakeToSpinalCase(): self
    {
        return $this->apply(function () {
            return str_replace('_', '-', $this->get());
        });
    }

    public function spinalToCamelCase(): self
    {
        return $this->apply(function () {
            return preg_replace_callback('{(-)([a-z])}', function ($match) {
                return mb_strtoupper($match[2]);
            }, $this->get());
        });
    }

    public function spinalToPascalCase(): self
    {
        return $this->apply(function () {
            return $this->spinalToCamelCase()->camelToPascalCase();
        });
    }

    public function spinalToSnakeCase(): self
    {
        return $this->apply(function () {
            return str_replace('-', '_', $this->get());
        });
    }

    public function toPhoneNumber(string $regex = '~.*(\d{3})[^\d]*(\d{3})[^\d]*(\d{4}).*~'): self
    {
        return $this->apply(function () use ($regex) {
            $number = preg_replace('/[^0-9]/', '', preg_replace($regex, '$1$2$3', $this->get()));

            if (7 > mb_strlen($number)) {
                return $this->get();
            }

            return 10 === mb_strlen($number) ? '1' . $number : $number;
        });
    }

    public function toPhoneFormat(): self
    {
        return $this->apply(function () {
            if (false === in_array(mb_strlen($string = $this->copy()->toPhoneNumber()->get()), [11, 7], true)) {
                return $this->get();
            }

            $number = sprintf('%s-%s', mb_substr($string, -7, 3), mb_substr($string, -4, 4));

            if (11 === mb_strlen($string)) {
                $number = sprintf('+%s (%s) ', mb_substr($string, 0, 1), mb_substr($string, 1, 3)) . $number;
            }

            return $number;
        });
    }

    /**
     * @return string[]
     */
    public function split(): array
    {
        return mb_str_split($this->__toString());
    }
}

/* EOF */
