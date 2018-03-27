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

final class StringTransform extends AbstractTransform
{
    /**
     * Construct by optionally setting the string value to manipulate.
     *
     * @param string $string
     * @param bool   $mutable
     */
    public function __construct($string = null, bool $mutable = false)
    {
        if (null !== $string) {
            parent::__construct($string, $mutable);
        } else {
            $this->setMutable($mutable);
        }
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException If a non string or non coercable string is provided.
     *
     * @return StringTransform
     */
    public function set($value) : TransformInterface
    {
        if (false === static::isConsumable($value)) {
            throw new \InvalidArgumentException('Value is not a string and could not be coerced to one.');
        }

        return parent::set((string) $value);
    }

    /**
     * @return string
     */
    public function get() : string
    {
        return parent::get();
    }

    /**
     * Perform string replacement using regular expression and replacement. Optionally turn regex inverse (negative)
     * and enforce case sensitivity.
     *
     * @param SearchReplaceRepresentative $config
     *
     * @return StringTransform|AbstractTransform
     */
    final public function replace(SearchReplaceRepresentative $config)
    {
        return $this->apply(function () use ($config) {
            return preg_replace($config->regex(), $config->replacement(), $this->get());
        });
    }

    /**
     * Perform conversion to alpha characters only.
     *
     * @return StringTransform|AbstractTransform
     */
    final public function toAlpha()
    {
        return $this->replace(new SearchReplaceRepresentative('', new RangedArchetype('a-z', true)));
    }

    /**
     * Perform conversion to numeric characters only.
     *
     * @return StringTransform|AbstractTransform
     */
    final public function toNumeric()
    {
        return $this->replace(new SearchReplaceRepresentative('', new RangedArchetype('0-9', true)));
    }

    /**
     * Perform conversion to alphanumeric characters only.
     *
     * @return StringTransform|AbstractTransform
     */
    final public function toAlphanumeric()
    {
        return $this->replace(new SearchReplaceRepresentative('', new RangedArchetype('a-z0-9', true)));
    }

    /**
     * Perform conversion to alphanumeric and dash characters only.
     *
     * @return StringTransform|AbstractTransform
     */
    final public function toAlphanumericAndDashes()
    {
        return $this->replace(new SearchReplaceRepresentative('', new RangedArchetype('a-z0-9-', true)));
    }

    /**
     * @return StringTransform|AbstractTransform
     */
    final public function spacesToDashes()
    {
        return $this->replace(new SearchReplaceRepresentative('-', new StringArchetype('[ ]+')));
    }

    /**
     * Perform conversion to alphanumeric and spaces to dashes only.
     *
     * @return StringTransform|AbstractTransform
     */
    final public function toAlphanumericAndSpacesToDashes()
    {
        return $this
            ->replace(new SearchReplaceRepresentative('-', new StringArchetype('[\s\n]+')))
            ->toAlphanumericAndDashes()
            ->replace(new SearchReplaceRepresentative('-', new StringArchetype('[-]+')));
    }

    /**
     * @return StringTransform|AbstractTransform
     */
    final public function dashesToSpaces()
    {
        return $this->replace(new SearchReplaceRepresentative(' ', new StringArchetype('[-]+')));
    }

    /**
     * @param bool $lowercase
     *
     * @return StringTransform|AbstractTransform
     */
    final public function slugify($lowercase = true)
    {
        return $this->apply(function (StringTransform $value) use ($lowercase) {
            $result = $value
                ->setMutable(true)
                ->replace(new SearchReplaceRepresentative('-', new RangedArchetype('a-z0-9-', true)))
                ->replace(new SearchReplaceRepresentative('-', new StringArchetype('[-]+')))
                ->replace((new SearchReplaceRepresentative('', new StringArchetype('[-]')))->enableAnchorLeft())
                ->replace((new SearchReplaceRepresentative('', new StringArchetype('[-]')))->enableAnchorRight())
                ->toLower();

            return $this->returnInstance($result->get());
        });
    }

    /**
     * @return StringTransform|AbstractTransform
     */
    final public function toLower()
    {
        return $this->apply(function () {
            return strtolower($this->get());
        });
    }

    /**
     * @return StringTransform|AbstractTransform
     */
    final public function toUpper()
    {
        return $this->apply(function () {
            return strtoupper($this->get());
        });
    }

    /**
     * @return StringTransform|AbstractTransform
     */
    final public function camelToPascalCase()
    {
        return $this->apply(function () {
            return ucfirst($this->get());
        });
    }

    /**
     * @return StringTransform|AbstractTransform
     */
    final public function camelToSnakeCase()
    {
        return $this->apply(function () {
            return preg_replace('#(?<=\\w)(?=[A-Z])#', '_$1', $this->get());
        })->toLower();
    }

    /**
     * @return StringTransform|AbstractTransform
     */
    final public function camelToSpinalCase()
    {
        return $this->apply(function () {
            return preg_replace('#(?<=\\w)(?=[A-Z])#', '-$1', $this->get());
        })->toLower();
    }

    /**
     * @return StringTransform|AbstractTransform
     */
    final public function pascalToCamelCase()
    {
        return $this->apply(function () {
            return lcfirst($this->get());
        });
    }

    /**
     * @return StringTransform|AbstractTransform
     */
    final public function pascalToSnakeCase()
    {
        return $this->apply(function () {
            return $this->camelToSnakeCase();
        });
    }

    /**
     * @return StringTransform|AbstractTransform
     */
    final public function pascalToSpinalCase()
    {
        return $this->apply(function () {
            return $this->camelToSpinalCase();
        });
    }

    /**
     * @return StringTransform|AbstractTransform
     */
    final public function snakeToCamelCase()
    {
        return $this->apply(function () {
            return preg_replace_callback('{(_)([a-z])}', function ($match) {
                return strtoupper($match[2]);
            }, $this->get());
        });
    }

    /**
     * @return StringTransform|AbstractTransform
     */
    final public function snakeToPascalCase()
    {
        return $this->apply(function () {
            return $this->snakeToCamelCase()->camelToPascalCase();
        });
    }

    /**
     * @return StringTransform|AbstractTransform
     */
    final public function snakeToSpinalCase()
    {
        return $this->apply(function () {
            return str_replace('_', '-', $this->get());
        });
    }

    /**
     * @return StringTransform|AbstractTransform
     */
    final public function spinalToCamelCase()
    {
        return $this->apply(function () {
            return preg_replace_callback('{(-)([a-z])}', function ($match) {
                return strtoupper($match[2]);
            }, $this->get());
        });
    }

    /**
     * @return StringTransform|AbstractTransform
     */
    final public function spinalToPascalCase()
    {
        return $this->apply(function () {
            return $this->spinalToCamelCase()->camelToPascalCase();
        });
    }

    /**
     * @return StringTransform|AbstractTransform
     */
    final public function spinalToSnakeCase()
    {
        return $this->apply(function () {
            return str_replace('-', '_', $this->get());
        });
    }

    /**
     * @param string $regex
     *
     * @return StringTransform|AbstractTransform|TransformInterface
     */
    final public function toPhoneNumber($regex = '~.*(\d{3})[^\d]*(\d{3})[^\d]*(\d{4}).*~')
    {
        return $this->apply(function () use ($regex) {
            $number = preg_replace('/[^0-9]/', '', preg_replace($regex, '$1$2$3', $this->get()));

            if (7 > strlen($number)) {
                return $this->get();
            }

            return 10 === strlen($number) ? '1'.$number : $number;
        });
    }

    /**
     * @return StringTransform|AbstractTransform|TransformInterface
     */
    final public function toPhoneFormat()
    {
        return $this->apply(function () {
            if (false === in_array(strlen($string = $this->copy()->toPhoneNumber()->get()), [11, 7])) {
                return $this->get();
            }

            $number = sprintf('%s-%s', substr($string, -7, 3), substr($string, -4, 4));

            if (11 === strlen($string)) {
                $number = sprintf('+%s (%s) ', substr($string, 0, 1), substr($string, 1, 3)).$number;
            }

            return $number;
        });
    }

    /**
     * @return string[]
     */
    final public function split() : array
    {
        return str_split($this->__toString());
    }
}

/* EOF */
