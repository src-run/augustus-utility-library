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

use SR\Silencer\CallSilencerFactory;
use SR\Utilities\Query\ClassQuery;

/**
 * Get file context based on a file path and line number.
 */
class FileContext implements FileContextInterface
{
    protected bool $initialized = false;

    protected ?\SplFileInfo $file = null;

    /**
     * @var string[]
     */
    protected array $contents = [];

    protected ?int $line = null;

    protected ?\ReflectionClass $class = null;

    protected ?\ReflectionMethod $method = null;

    /**
     * Define the context by specifying a file path and line number.
     */
    public function __construct(string $file, int $line)
    {
        $this->file = new \SplFileInfo($file);
        $this->line = $line;
    }

    /**
     * Get the file line number for the defined context.
     */
    public function getLine(): int
    {
        return $this->line;
    }

    /**
     * Get a \SplFileInfo instance for the defined context.
     */
    public function getFile(): \SplFileInfo
    {
        return $this->file;
    }

    /**
     * Get the file path name for the defined context.
     */
    public function getFilePathname(): string
    {
        return $this->file->getPathname();
    }

    /**
     * Get the file contents for the defined context.
     *
     * @return string[]
     */
    public function getFileContents(): array
    {
        return $this->init()->contents;
    }

    /**
     * Get an array of file lines surrounding defined context.
     *
     * @return string[]
     */
    public function getFileContext(int $surroundingLines = 3): array
    {
        return $this->init()->createFileContextSnippet($surroundingLines);
    }

    /**
     * Get the file line content for the defined context.
     */
    public function getFileContextLine(): string
    {
        $line = $this->getFileContext(1);

        return 1 === count($line) ? array_shift($line) : '';
    }

    /**
     * Returns the context type (trait, interface, or class).
     */
    public function getType(): string
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
     */
    public function getClass(): \ReflectionClass
    {
        return $this->init()->class;
    }

    /**
     * Get the class name (as qualified or unqualified) for the defined context.
     */
    public function getClassName(bool $qualified = true): string
    {
        return $qualified ? $this->getClass()->getName() : $this->getClass()->getShortName();
    }

    /**
     * Returns true if a method exists for this context.
     */
    public function hasMethod(): bool
    {
        return null !== $this->method;
    }

    /**
     * Get the method reflection instance.
     */
    public function getMethod(): \ReflectionMethod
    {
        if (null === $method = $this->init()->method) {
            throw new \RuntimeException('No method exists for context');
        }

        return $method;
    }

    /**
     * Get the method name.
     */
    public function getMethodName(bool $qualified = false): string
    {
        $method = $this->getMethod()->getShortName();

        return $qualified ? sprintf('%s::%s', $this->getClassName(true), $method) : $method;
    }

    /**
     * @return string[]
     */
    private function createFileContextSnippet(int $lines): array
    {
        if (0 > $lines) {
            $lines = 0;
        }

        for ($i = $this->line - $lines - 1; $i < $this->line + $lines; ++$i) {
            if (isset($this->contents[$i])) {
                $diff[] = $this->contents[$i];
            }
        }

        return $diff;
    }

    private function init(): self
    {
        if (true !== $this->initialized) {
            return $this->initContext();
        }

        return $this;
    }

    private function initContext(): self
    {
        try {
            $this->contents = $this->initContextContents();
            $this->class = $this->initContextReflectionClass();
            $this->method = $this->initContextReflectionMethod();
        } catch (\RuntimeException $e) {
            throw new \RuntimeException('Could not initialize file context!', 0, $e);
        }

        $this->initialized = true;

        return $this;
    }

    /**
     * @return string[]
     */
    private function initContextContents(): array
    {
        $contents = CallSilencerFactory::create(function () {
            return @file_get_contents($this->file);
        }, function ($return, $raised): bool {
            return null === $raised && $return;
        })->invoke();

        return $contents->isValid() ? explode(PHP_EOL, $contents->getReturn()) : [];
    }

    /**
     * @return \ReflectionClass|\ReflectionObject|null
     */
    private function initContextReflectionClass(): ?\ReflectionClass
    {
        return ClassQuery::tryReflection(sprintf(
            '%s\\%s', $this->searchFileForNamespace(), $this->searchFileForClassName()
        ));
    }

    private function initContextReflectionMethod(): ?\ReflectionMethod
    {
        return array_values(array_filter($this->class->getMethods(), function (\ReflectionMethod $method) {
            return $method->getDeclaringClass()->getName() === $this->class->getName() &&
                $method->getStartLine() <= $this->line && $this->line <= $method->getEndLine();
        }))[0] ?? null;
    }

    private function searchFileForNamespace(): string
    {
        return $this->searchFile('^(?:namespace[\s]+)([^\s\n]+);');
    }

    private function searchFileForClassName(): string
    {
        return $this->searchFile('^(?:abstract|final[\s]+)?(?:class|trait|interface)\s+([^\s\n\{]+)');
    }

    private function searchFile(string $regex): string
    {
        if (null === $this->contents || 0 === count($this->contents)) {
            return '';
        }

        if (1 !== @preg_match(sprintf('{%s}mi', $regex), implode(PHP_EOL, $this->contents), $matches)) {
            return '';
        }

        return array_pop($matches);
    }
}

/* EOF */
