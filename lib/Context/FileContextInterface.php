<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Utilities\Context;

/**
 * File context interface.
 */
interface FileContextInterface
{
    /**
     * Get the file line number for the defined context.
     */
    public function getLine(): int;

    /**
     * Get a \SplFileInfo instance for the defined context.
     */
    public function getFile(): \SplFileInfo;

    /**
     * Get the file path name for the defined context.
     */
    public function getFilePathname(): string;

    /**
     * Get the file contents for the defined context.
     *
     * @return string[]
     */
    public function getFileContents(): array;

    /**
     * Get an array of file lines surrounding defined context.
     *
     * @return string[]
     */
    public function getFileContext(int $surroundingLines = 3): array;

    /**
     * Get the file line content for the defined context.
     */
    public function getFileContextLine(): string;

    /**
     * Get a \ReflectionClass instance for the defined context.
     */
    public function getClass(): \ReflectionClass;

    /**
     * Get the class name (as qualified or unqualified) for the defined context.
     */
    public function getClassName(bool $qualified = true): string;

    /**
     * Returns the context type (trait, interface, or class).
     */
    public function getType(): string;

    /**
     * Returns true if a method exists for this context.
     */
    public function hasMethod(): bool;

    /**
     * Get the method reflection instance.
     */
    public function getMethod(): \ReflectionMethod;

    /**
     * Get the method name.
     */
    public function getMethodName(bool $qualified = false): string;
}

/* EOF */
