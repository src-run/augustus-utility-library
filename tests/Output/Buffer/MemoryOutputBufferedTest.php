<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Utils\Tests\Output\Buffered\Buffer;

use PHPUnit\Framework\TestCase;
use SR\Output\Buffered\MemoryOutputBuffered;
use Symfony\Component\Finder\Finder;

/**
 * @covers \SR\Output\Buffered\MemoryOutputBuffered
 */
class MemoryOutputBufferedTest extends TestCase
{
    /**
     * @return \Iterator
     */
    public static function provideTestSchemeData(): \Iterator
    {
        yield ['php://memory', null];
        yield ['php://temp/maxmemory:1048576', 1];
        yield ['php://temp/maxmemory:10485760', 10];
        yield ['php://temp/maxmemory:2097152000', 2000];
    }

    /**
     * @dataProvider provideTestSchemeData
     *
     * @param string   $expectedScheme
     * @param int|null $maxMemory
     */
    public function testScheme(string $expectedScheme, int $maxMemory = null): void
    {
        $this->assertSame($expectedScheme, (new MemoryOutputBuffered($maxMemory))->getScheme());
    }

    /**
     * @return \Iterator
     */
    public static function provideFileData(): \Iterator
    {
        $files = array_map(function (\SplFileInfo $file): string {
            return (string) $file;
        }, iterator_to_array(
            (new Finder())
                ->in(sprintf('%s/../../', __DIR__))
                ->name('*.php')
                ->files()
        ));

        shuffle($files);
        $count = count($files);

        for ($i = mt_rand(0, $count / 10); $i < $count - 1; $i += mt_rand($count / 6, $count / 4)) {
            yield [$files[$i], $files[$i + 1], null];
            yield [$files[$i], $files[$i + 1], 1];
        }
    }

    /**
     * @dataProvider provideFileData
     *
     * @param string   $fileOne
     * @param string   $fileTwo
     * @param int|null $maxMemory
     */
    public function testSimultaneousReadAndWrite(string $fileOne, string $fileTwo, int $maxMemory = null): void
    {
        $bufferOne = new MemoryOutputBuffered($maxMemory);
        $bufferTwo = new MemoryOutputBuffered($maxMemory);

        $this->assertBufferIsOpen($bufferOne);
        $this->assertBufferIsOpen($bufferTwo);

        $this->assertBufferCanWriteAndRead($bufferOne, $contentsOne = file_get_contents($fileOne));
        $this->assertBufferCanWriteAndRead($bufferTwo, $contentsTwo = file_get_contents($fileTwo));

        $this->assertBufferNotContains($bufferOne, $contentsTwo);
        $this->assertBufferNotContains($bufferTwo, $contentsOne);

        $bufferOne->reset();

        $this->assertBufferIsOpen($bufferOne);
        $this->assertBufferIsOpen($bufferTwo);

        $this->assertBufferContains($bufferOne, '');
        $this->assertBufferContains($bufferTwo, $contentsTwo.PHP_EOL);

        $bufferTwo->reset();

        $this->assertBufferIsOpen($bufferOne);
        $this->assertBufferIsOpen($bufferTwo);

        $this->assertBufferContains($bufferOne, '');
        $this->assertBufferContains($bufferTwo, '');

        $bufferOne->close();

        $this->assertBufferIsNotOpen($bufferOne);
        $this->assertBufferIsOpen($bufferTwo);
        $this->assertBufferCanWriteAndRead($bufferTwo, $contentsTwo);

        $bufferTwo->close();

        $this->assertBufferIsNotOpen($bufferOne);
        $this->assertBufferIsNotOpen($bufferTwo);
    }

    public function testThrowsOnWriteWhenClosed(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageRegExp('{Failed to write "foobar" data to closed buffer: re-open the buffer resource using the "[^"]+MemoryOutputBuffered::reset\(\)" method\.}');

        $buffer = new MemoryOutputBuffered();
        $buffer->close();
        $buffer->add('foobar');
    }

    public function testThrowsOnReadWhenClosed(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageRegExp('{Failed to read "all" data from closed buffer: re-open the buffer resource using the "[^"]+MemoryOutputBuffered::reset\(\)" method\.}');

        $buffer = new MemoryOutputBuffered();
        $buffer->add('foobar');
        $buffer->close();
        $buffer->get();
    }

    /**
     * @param MemoryOutputBuffered $buffer
     * @param string               $contents
     */
    private function assertBufferCanWriteAndRead(MemoryOutputBuffered $buffer, string $contents): void
    {
        $this->assertEmpty($buffer->get());

        foreach (explode(PHP_EOL, $contents) as $line) {
            $buffer->add($line, true);
            $this->assertContains($line.PHP_EOL, (string) $buffer);
            $this->assertContains($line.PHP_EOL, $buffer->get());
        }

        $this->assertBufferContains($buffer, $contents.PHP_EOL);
    }

    /**
     * @param MemoryOutputBuffered $buffer
     * @param string               $contents
     */
    private function assertBufferNotContains(MemoryOutputBuffered $buffer, string $contents = ''): void
    {
        $this->assertNotSame($contents, $buffer->get());
        $this->assertNotSame($contents, (string) $buffer);
    }

    /**
     * @param MemoryOutputBuffered $buffer
     * @param string               $contents
     */
    private function assertBufferContains(MemoryOutputBuffered $buffer, string $contents = ''): void
    {
        $this->assertSame($contents, $buffer->get());
        $this->assertSame($contents, (string) $buffer);
    }

    /**
     * @param MemoryOutputBuffered $buffer
     */
    private function assertBufferIsOpen(MemoryOutputBuffered $buffer): void
    {
        $this->assertTrue($buffer->isResourceOpen());
        $this->assertInternalType('resource', $buffer->getResource());
    }

    /**
     * @param MemoryOutputBuffered $buffer
     */
    private function assertBufferIsNotOpen(MemoryOutputBuffered $buffer): void
    {
        $this->assertFalse($buffer->isResourceOpen());
        $this->assertInternalType('resource', $buffer->getResource());
    }
}
