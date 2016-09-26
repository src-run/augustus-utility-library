<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Util\Context;

use SR\Silencer\CallSilencer;

/**
 * Get file context based on a file path and line number.
 */
class FileContext implements FileContextInterface
{
    /**
     * @var bool
     */
    protected $initialized = false;

    /**
     * @var \SplFileInfo
     */
    protected $file;

    /**
     * @var string[]
     */
    protected $contents;

    /**
     * @var int
     */
    protected $line;

    /**
     * @var \ReflectionClass
     */
    protected $class;

    /**
     * @var \ReflectionMethod
     */
    protected $method;

    /**
     * Define the context by specifying a file path and line number.
     *
     * @param string $file
     * @param int    $line
     */
    public function __construct(string $file, int $line)
    {
        $this->file = new \SplFileInfo($file);
        $this->line = (int) $line;
    }

    /**
     * Get the file line number for the defined context.
     *
     * @return int
     */
    public function getLine() : int
    {
        return $this->line;
    }

    /**
     * Get a \SplFileInfo instance for the defined context.
     *
     * @return \SplFileInfo
     */
    public function getFile() : \SplFileInfo
    {
        return $this->file;
    }

    /**
     * Get the file path name for the defined context.
     *
     * @return string
     */
    public function getFilePathname() : string
    {
        return $this->file->getPathname();
    }

    /**
     * Get the file contents for the defined context.
     *
     * @return string[]
     */
    public function getFileContents() : array
    {
        return $this->init()->contents;
    }

    /**
     * Get an array of file lines surrounding defined context.
     *
     * @param int $surroundingLines
     *
     * @return string[]
     */
    public function getFileContext(int $surroundingLines = 3) : array
    {
        return $this->init()->createFileLineSnippet($surroundingLines);
    }

    /**
     * Get the file line content for the defined context.
     *
     * @return string
     */
    public function getFileContextLine() : string
    {
        $line = $this->getFileContext(1);

        return count($line) === 1 ? array_shift($line) : '';
    }

    /**
     * Returns the context type (trait, interface, or class).
     *
     * @return string
     */
    public function getType() : string
    {
        if ($this->getClass()->isTrait()) {
            return 'trait';
        } elseif ($this->getClass()->isInterface()) {
            return 'interface';
        }

        return 'class';
    }

    /**
     * Get a \ReflectionClass instance for the defined context.
     *
     * @return \ReflectionClass
     */
    public function getClass() : \ReflectionClass
    {
        return $this->init()->class;
    }

    /**
     * Get the class name (as qualified or unqualified) for the defined context.
     *
     * @param bool $qualified
     *
     * @return string
     */
    public function getClassName(bool $qualified = true) : string
    {
        return $qualified ? $this->getClass()->getName() : $this->getClass()->getShortName();
    }

    /**
     * Returns true if a method exists for this context.
     *
     * @return bool
     */
    public function hasMethod() : bool
    {
        return $this->method !== null;
    }

    /**
     * Get the method reflection instance.
     *
     * @return \ReflectionMethod
     */
    public function getMethod() : \ReflectionMethod
    {
        if (null === $method = $this->init()->method) {
            throw new \RuntimeException('No method exists for context');
        }

        return $method;
    }

    /**
     * Get the method name.
     *
     * @param bool $qualified
     *
     * @return string
     */
    public function getMethodName(bool $qualified = false) : string
    {
        $method = $this->getMethod()->getShortName();

        return $qualified ? sprintf('%s::%s', $this->getClassName(true), $method) : $method;
    }

    /**
     * @param int $surroundingLines
     *
     * @return string[]
     */
    private function createFileLineSnippet(int $surroundingLines) : array
    {
        $diff = [];
        $min = $this->line - $surroundingLines - 1;
        $max = $this->line + $surroundingLines;

        for ($i = $min; $i < $max; ++$i) {
            if (isset($this->contents[$i])) {
                $diff[] = $this->contents[$i];
            }
        }

        return $diff;
    }

    /**
     * @throws \RuntimeException
     *
     * @return FileContext
     */
    private function init() : FileContext
    {
        if (true !== $this->initialized) {
            return $this->initContext();
        }

        return $this;
    }

    /**
     * @return FileContext
     */
    private function initContext() : FileContext
    {
        try {
            $this->initContextContents();
            $this->initContextReflectionClass();
            $this->initContextReflectionMethod();
        } catch (\RuntimeException $e) {
            throw new \RuntimeException('Could not initialize file context!', null, $e);
        }

        $this->initialized = true;

        return $this;
    }

    /**
     * @return FileContext
     */
    private function initContextContents() : FileContext
    {
        $silencer = CallSilencer::create(function () {
            return @file_get_contents($this->file);
        })->invoke();

        if (!$silencer->isResultFalse()) {
            $this->contents = explode(PHP_EOL, $silencer->getResult());
        }

        return $this;
    }

    /**
     * @return FileContext
     */
    private function initContextReflectionClass() : FileContext
    {
        $namespace = $this->searchFileForNamespace();
        $className = $this->searchFileForClassName();

        $this->class = new \ReflectionClass($namespace.'\\'.$className);

        return $this;
    }

    /**
     * @return FileContext
     */
    private function initContextReflectionMethod() : FileContext
    {
        $search = array_filter($this->class->getMethods(), function (\ReflectionMethod $m) {
            return $m->getStartLine() <= $this->line && $this->line <= $m->getEndLine();
        });

        if (count($search) === 1) {
            $this->method = $search[0];
        }

        return $this;
    }

    /**
     * @return string
     */
    private function searchFileForNamespace() : string
    {
        return $this->searchFile('^(namespace)\s+([^\s\n]+);');
    }

    /**
     * @return string
     */
    private function searchFileForClassName() : string
    {
        return $this->searchFile('^(class|trait|interface)\s+([^\s\n\{]+)');
    }

    /**
     * @param string $regex
     *
     * @return string
     */
    private function searchFile(string $regex) : string
    {
        if (null === $this->contents || count($this->contents) === 0) {
            return '';
        }

        if (1 !== @preg_match(sprintf('{%s}mi', $regex), implode(PHP_EOL, $this->contents), $matches)) {
            return '';
        }

        return array_pop($matches);
    }
}

/* EOF */
