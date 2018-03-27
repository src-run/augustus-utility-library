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
     *
     * @return int
     */
    public function getLine() : int;

    /**
     * Get a \SplFileInfo instance for the defined context.
     *
     * @return \SplFileInfo
     */
    public function getFile() : \SplFileInfo;

    /**
     * Get the file path name for the defined context.
     *
     * @return string
     */
    public function getFilePathname() : string;

    /**
     * Get the file contents for the defined context.
     *
     * @return string[]
     */
    public function getFileContents() : array;

    /**
     * Get an array of file lines surrounding defined context.
     *
     * @param int $surroundingLines
     *
     * @return string[]
     */
    public function getFileContext(int $surroundingLines = 3) : array;

    /**
     * Get the file line content for the defined context.
     *
     * @return string
     */
    public function getFileContextLine() : string;

    /**
     * Get a \ReflectionClass instance for the defined context.
     *
     * @return \ReflectionClass
     */
    public function getClass() : \ReflectionClass;

    /**
     * Get the class name (as qualified or unqualified) for the defined context.
     *
     * @param bool $qualified
     *
     * @return string
     */
    public function getClassName(bool $qualified = true) : string;

    /**
     * Returns the context type (trait, interface, or class).
     *
     * @return string
     */
    public function getType() : string;

    /**
     * Returns true if a method exists for this context.
     *
     * @return bool
     */
    public function hasMethod() : bool;

    /**
     * Get the method reflection instance.
     *
     * @return \ReflectionMethod
     */
    public function getMethod() : \ReflectionMethod;

    /**
     * Get the method name.
     *
     * @param bool $qualified
     *
     * @return string
     */
    public function getMethodName(bool $qualified = false) : string;
}

/* EOF */
