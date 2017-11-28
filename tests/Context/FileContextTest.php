<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Util\Test\Context;

use SR\Util\Context\FileContext;
use SR\Util\Test\Fixture\FixtureInterface;
use SR\Util\Test\Fixture\FixtureTrait;

class FileContextTest extends \PHPUnit_Framework_TestCase
{
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
        $context = $this->instantiateFileContext();

        $this->assertNotNull($context->getFileContextLine());
        $this->assertCount(9, $context->getFileContext(4));
        $this->assertCount(5, $context->getFileContext(2));
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
        $context = $this->instantiateFileContext(__DIR__ . '/../Fixture/data-provider_transform-string.yml');

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
}
