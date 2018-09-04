<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Tests\Utilities\IO\Buffered;

use PHPUnit\Framework\TestCase;
use SR\Utilities\IO\Buffered\BufferedInterface;
use SR\Utilities\IO\Buffered\Input\MemoryInputBuffered;
use SR\Utilities\IO\Buffered\Output\MemoryOutputBuffered;
use Symfony\Component\Finder\Finder;

abstract class MemoryBufferedTestCase extends TestCase
{
    /**
     * @return \Generator|int[]
     */
    public static function provideMemoryData(): \Generator
    {
        yield [null, null];

        for ($negative = -1; $negative > -10240; $negative-= mt_rand(1024, 2048)) {
            yield [$negative, null];
        }

        $addRandomDecimal = function (int $number, int $randMin = 0, int $randMax = 999): float {
            return (float) sprintf('%d.%02d', $number, mt_rand($randMin, $randMax));
        };

        for ($megabytes = 0; $megabytes < 20480; $megabytes += $addRandomDecimal(mt_rand(1024, 2048))) {
            yield [$megabytes, self::convertMegabytesToBytes($megabytes)];
        }
    }

    /**
     * @dataProvider provideMemoryData
     *
     * @param float|null $megabytes
     * @param int|null   $bytes
     */
    public function testMemory(?float $megabytes, ?int $bytes): void
    {
        $this->assertSame($megabytes, ($buffer = static::createMemoryBufferedInstance($megabytes))->memory());

        if (null !== $bytes) {
            $this->assertContains((string) $bytes, $buffer->scheme());
        } else {
            $this->assertNotContains((string) self::convertMegabytesToBytes($megabytes ?? 0), $buffer->scheme());
        }
    }

    /**
     * @return \Generator
     */
    public static function provideModeData(): \Generator
    {
        $modes = ['r', 'r+', 'w', 'w+', 'a', 'a+', 'x', 'x+', 'c', 'c+', 'e'];
        shuffle($modes);

        foreach (self::provideMemoryData() as list($megabytes, $bytes)) {
            yield [$megabytes, null, 'x+b', $bytes];
            yield [$megabytes, $modes[mt_rand(0, count($modes) - 1)], null, $bytes];
        }
    }

    /**
     * @dataProvider provideModeData
     *
     * @param float|null  $megabytes
     * @param null|string $providedMode
     * @param null|string $expectedMode
     */
    public function testMode(?float $megabytes, ?string $providedMode, string $expectedMode = null): void
    {
        $this->assertSame($expectedMode ?? $providedMode, (static::createMemoryBufferedInstance($megabytes, $providedMode))->mode());
    }

    /**
     * @return \Generator
     */
    public static function provideSchemeData(): \Generator
    {
        foreach (self::provideMemoryData() as list($megabytes, $bytes)) {
            yield [$megabytes, self::createSchemeString($megabytes, $bytes)];
        }
    }

    /**
     * @dataProvider provideSchemeData
     *
     * @param float|null $megabytes
     * @param string     $expectedScheme
     */
    public function testScheme(?float $megabytes, string $expectedScheme): void
    {
        $this->assertSame($expectedScheme, (static::createMemoryBufferedInstance($megabytes))->scheme());
    }

    /**
     * @return \Generator
     */
    public static function provideFullConstructionData(): \Generator
    {
        foreach (self::provideModeData() as list($megabytes, $providedMode, $expectedMode, $bytes)) {
            yield [$megabytes, $providedMode, $expectedMode ?? $providedMode, self::createSchemeString($megabytes, $bytes)];
        }
    }

    /**
     * @dataProvider provideFullConstructionData
     *
     * @param float|null  $megabytes
     * @param null|string $providedMode
     * @param string      $expectedMode
     * @param string      $expectedSchema
     */
    public function testFullConstruction(?float $megabytes, ?string $providedMode, string $expectedMode, string $expectedSchema): void
    {
        $buffer = static::createMemoryBufferedInstance($megabytes, $providedMode);

        $this->assertSame($expectedMode, $buffer->mode());
        $this->assertSame($megabytes, $buffer->memory());
        $this->assertSame($expectedSchema, $buffer->scheme());
    }

    /**
     * @dataProvider provideSchemeData
     *
     * @param float|null $megabytes
     */
    public function testThrowsOnSetMemoryWhileResourceActive(?float $megabytes): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageRegExp('/Cannot set memory while resource is open. Close resource with .+Memory(Output|Input)Buffered::close()./');

        $buffer = static::createMemoryBufferedInstance($megabytes);
        $buffer->setMemory($megabytes - 1024);
    }

    /**
     * @dataProvider provideSchemeData
     *
     * @param float|null $megabytes
     */
    public function testThrowsOnSetModeWhileResourceActive(?float $megabytes): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageRegExp('/Cannot set mode while resource is open. Close resource with .+Memory(Output|Input)Buffered::close()./');

        $buffer = static::createMemoryBufferedInstance($megabytes);
        $buffer->setMode('r+');
    }

    public function testThrowsOnFileOpenError(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageRegExp('/Failed to open "[^"]+" \(mode: "[^"]+"; limit: "[^\s]+ megabytes \/ [^\s]+ bytes"\): .+/i');

        $b = static::createMemoryBufferedInstance();
        $b->close();
        $p = (new \ReflectionObject($b))->getProperty('scheme');
        $p->setAccessible(true);
        $p->setValue($b, 'php://invalid-type-identifier');
        $b->reset();
    }

    /**
     * @dataProvider provideSchemeData
     *
     * @param float|null $megabytes
     * @param string     $expectedScheme
     */
    public function testChangingMemoryAndModeAllocationAfterClosingResource(?float $megabytes, string $expectedScheme): void
    {
        $this->assertSame($expectedScheme, ($buffer = static::createMemoryBufferedInstance($megabytes))->scheme());
        $buffer->close();
        $buffer->setMode('r');
    }

    /**
     * @return \Generator
     */
    public static function provideFileData(): \Generator
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
     * @param int|null $megabytes
     */
    public function testSimultaneousReadAndWrite(string $fileOne, string $fileTwo, int $megabytes = null): void
    {
        $bufferOne = static::createMemoryBufferedInstance($megabytes);
        $bufferTwo = static::createMemoryBufferedInstance($megabytes);

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
        $this->expectExceptionMessageRegExp('{Failed to write "foobar" data to closed buffer: re-open the buffer resource using the "[^"]+Memory(Output|Input)Buffered::reset\(\)" method\.}');

        $buffer = static::createMemoryBufferedInstance();
        $buffer->close();
        $buffer->add('foobar');
    }

    public function testThrowsOnReadWhenClosed(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageRegExp('{Failed to read "all" data from closed buffer: re-open the buffer resource using the "[^"]+Memory(Output|Input)Buffered::reset\(\)" method\.}');

        $buffer = static::createMemoryBufferedInstance();
        $buffer->add('foobar');
        $buffer->close();
        $buffer->get();
    }

    /**
     * @param float|null  $memory
     * @param string|null $mode
     *
     * @return BufferedInterface|MemoryOutputBuffered|MemoryInputBuffered
     */
    abstract protected static function createMemoryBufferedInstance(float $memory = null, string $mode = null): BufferedInterface;

    /**
     * @param float $megabytes
     *
     * @return int
     */
    private static function convertMegabytesToBytes(float $megabytes): int
    {
        $m = (new \ReflectionObject(static::createMemoryBufferedInstance()))->getMethod(__FUNCTION__);
        $m->setAccessible(true);

        return $m->invoke(null, $megabytes);
    }

    /**
     * @param float|null $megabytes
     * @param int|null   $bytes
     *
     * @return string
     */
    private static function createSchemeString(?float $megabytes, ?int $bytes): string
    {
        if (null === $megabytes) {
            return 'php://memory';
        }

        if (0 > $megabytes) {
            return 'php://temp';
        }

        if (null === $bytes) {
            self::fail(vsprintf('Invalid parameters for "%s" provided: (%s, %s).', [
                __METHOD__,
                var_export($megabytes, true),
                var_export($bytes, true),
            ]));
        }

        return sprintf('php://temp/maxmemory:%d', $bytes);
    }

    /**
     * @param BufferedInterface $buffer
     * @param string            $contents
     */
    private function assertBufferCanWriteAndRead(BufferedInterface $buffer, string $contents): void
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
     * @param BufferedInterface $buffer
     * @param string            $contents
     */
    private function assertBufferNotContains(BufferedInterface $buffer, string $contents = ''): void
    {
        $this->assertNotSame($contents, $buffer->get());
        $this->assertNotSame($contents, (string) $buffer);
    }

    /**
     * @param BufferedInterface $buffer
     * @param string            $contents
     */
    private function assertBufferContains(BufferedInterface $buffer, string $contents = ''): void
    {
        $this->assertSame($contents, $buffer->get());
        $this->assertSame($contents, (string) $buffer);
    }

    /**
     * @param BufferedInterface $buffer
     */
    private function assertBufferIsOpen(BufferedInterface $buffer): void
    {
        $this->assertTrue($buffer->isResourceOpen());
        $this->assertInternalType('resource', $buffer->resource());
    }

    /**
     * @param BufferedInterface $buffer
     */
    private function assertBufferIsNotOpen(BufferedInterface $buffer): void
    {
        $this->assertFalse($buffer->isResourceOpen());
        $this->assertInternalType('resource', $buffer->resource());
    }
}
