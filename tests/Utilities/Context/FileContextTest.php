<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Utilities\Test\Context;

use PHPUnit\Framework\TestCase;
use SR\Utilities\Context\FileContext;
use SR\Utilities\Test\Fixture\FixtureInterface;
use SR\Utilities\Test\Fixture\FixtureTrait;

/**
 * @covers \SR\Utilities\Context\FileContext
 */
class FileContextTest extends TestCase
{
    public function testGetLine()
    {
        $context = $this->instantiateFileContext();

        $this->assertNotNull($context->getLine());
    }

    public function testGetMethod()
    {
        $method = 'instantiateFileContext';
        $context = $this->instantiateFileContext();

        $this->assertInstanceOf(\ReflectionMethod::class, $context->getMethod());
        $this->assertSame(__CLASS__.'::'.$method, $context->getMethodName(true));
        $this->assertSame($method, $context->getMethodName());
    }

    public function testGetClass()
    {
        $context = $this->instantiateFileContext();

        $this->assertInstanceOf(\ReflectionClass::class, $context->getClass());
        $this->assertSame(__CLASS__, $context->getClassName(true));
    }

    public function testGetFile()
    {
        $context = $this->instantiateFileContext();

        $this->assertInstanceOf(\SplFileInfo::class, $context->getFile());
        $this->assertSame(__FILE__, $context->getFilePathname());
    }

    public function testGetFileDiff()
    {
        $reflected = (new \ReflectionObject($this))->getMethod(__FUNCTION__);
        $linesFile = count(explode(PHP_EOL, file_get_contents($reflected->getFileName())));
        $linesComp = min($reflected->getStartLine(), $linesFile - $reflected->getStartLine());

        $context = $this->instantiateFileContext($reflected->getFileName(), $reflected->getStartLine());
        $this->assertNotNull($context->getFileContextLine());

        for ($i = 0; $i < $linesComp; ++$i) {
            $this->assertCount(1 + ($i * 2), $context->getFileContext($i));
        }
    }

    public static function provideGetFileDiffAtEndOrBeginningOfFileData(): \Iterator
    {
        yield ['testGetLine'];
        yield ['instantiateFileContext'];
    }

    /**
     * @dataProvider provideGetFileDiffAtEndOrBeginningOfFileData
     *
     * @param string $method
     */
    public function testGetFileDiffAtEndOrBeginningOfFile(string $method)
    {
        $reflected = (new \ReflectionObject($this))->getMethod($method);
        $linesFile = count(explode(PHP_EOL, file_get_contents($reflected->getFileName())));
        $linesGoDn = $linesFile - $reflected->getStartLine();
        $linesGoUp = $reflected->getStartLine() - 1;

        $context = $this->instantiateFileContext($reflected->getFileName(), $reflected->getStartLine());
        $this->assertNotNull($context->getFileContextLine());

        for ($i = 0; $i < $linesFile; ++$i) {
            $this->assertCount(min(1 + $i + min($i, $linesGoDn, $linesGoUp), $linesFile), $context->getFileContext($i));
        }
    }

    public function testGetFileDiffIgnoresNegativeInput()
    {
        $context = $this->instantiateFileContext();

        for ($i = -1; $i > -20; --$i) {
            $this->assertCount(1, $context->getFileContext($i));
        }
    }

    public function testGetFileContents()
    {
        $context = $this->instantiateFileContext();

        $this->assertSame(file_get_contents(__FILE__), implode(PHP_EOL, $context->getFileContents()));
    }

    public function testGetType()
    {
        $context = $this->instantiateFileContext();

        $this->assertSame('class', $context->getType());
    }

    public function testUsingTrait()
    {
        $context = $this->instantiateFileContext($file = __DIR__.'/../Fixture/FixtureTrait.php', 18);

        $this->assertSame(FixtureTrait::class, $context->getClassName());
        $this->assertSame('trait', $context->getType());
    }

    public function testUsingInterface()
    {
        $context = $this->instantiateFileContext($file = __DIR__.'/../Fixture/FixtureInterface.php', 18);

        $this->assertSame(FixtureInterface::class, $context->getClassName());
        $this->assertSame('interface', $context->getType());
    }

    public function testNoMethod()
    {
        $context = $this->instantiateFileContext(null, 1000000);

        $this->assertFalse($context->hasMethod());

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No method exists for context');

        $context->getMethod();
    }

    public function testThrowsExceptionOnNotFoundFile()
    {
        $context = $this->instantiateFileContext('/tmp/does/not/exist');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Could not initialize file context');

        $context->getClass();
    }

    public function testThrowsExceptionOnNotFoundClass()
    {
        $context = $this->instantiateFileContext(__DIR__.'/../Fixture/data-provider_transform-string.yml');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Could not initialize file context');

        $context->getClass();
    }

    public function testThrowsExceptionOnNotFoundNamespaceOrClass()
    {
        $context = $this->instantiateFileContext(__DIR__.'/../Fixture/NoClass.php');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Could not initialize file context');

        $context->getClass();
    }

    /**
     * @param string|null $file
     * @param int|null    $line
     *
     * @return FileContext
     */
    protected function instantiateFileContext($file = null, $line = null)
    {
        return new FileContext($file ?: __FILE__, $line ?: __LINE__);
    }
}
