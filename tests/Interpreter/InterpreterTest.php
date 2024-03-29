<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Tests\Utilities\Interpreter;

use PHPUnit\Framework\TestCase;
use SR\Utilities\Interpreter\Backtrace\Backtrace;
use SR\Utilities\Interpreter\Backtrace\BacktraceRecord;
use SR\Utilities\Interpreter\Error\Error;
use SR\Utilities\Interpreter\Interpreter;
use SR\Utilities\Interpreter\Reporting\ReportingLevel;

/**
 * @covers \SR\Utilities\Interpreter\Interpreter
 * @covers \SR\Utilities\Interpreter\Error\Error
 * @covers \SR\Utilities\Interpreter\Backtrace\Backtrace
 * @covers \SR\Utilities\Interpreter\Backtrace\BacktraceRecord
 * @covers \SR\Utilities\Interpreter\Reporting\ReportingLevel
 */
class InterpreterTest extends TestCase
{
    public static function provideErrorData(): \Iterator
    {
        yield [function () {
            @file_get_contents(sprintf('%s/foo/bar/baz.ext', sys_get_temp_dir()));
        }, 'file_get_contents(%s/foo/bar/baz.ext): %sailed to open stream: No such file or directory'];
        yield [function () {
            @unlink(sprintf('%s/foo/bar/baz.ext', sys_get_temp_dir()));
        }, 'unlink(%s/foo/bar/baz.ext): No such file or directory'];
    }

    /**
     * @dataProvider provideErrorData
     */
    public function testError(\Closure $errorCauser, string $expectedMessageFormat)
    {
        $errorCauser();

        $this->assertTrue(Interpreter::hasError());
        $this->assertTrue(Interpreter::hasError());
        $error = Interpreter::clearError();
        $this->assertFalse(Interpreter::hasError());

        $errorCauser();
        $error = Interpreter::error(false);
        $this->assertTrue(Interpreter::hasError());
        $error = Interpreter::error();
        unset($error);
        $this->assertFalse(Interpreter::hasError());

        $errorCauser();
        $this->assertTrue(Interpreter::hasError());
        $error = Interpreter::error();
        $this->assertTrue(Interpreter::hasError());
        $this->assertStringMatchesFormat($expectedMessageFormat, $error->text());

        $this->assertValidTrace($error->trace());

        $this->assertIsInt($error->type());
        $this->assertInstanceOf(\SplFileInfo::class, $error->file());
        $this->assertIsInt($error->line());
        $this->assertInstanceOf(Backtrace::class, $error->trace());
        $this->assertTrue($error->hasFile());
        $this->assertTrue($error->hasTrace());
        $this->assertTrue($error->isReal());
        $this->assertFalse($error->isMock());
        unset($error);
        $this->assertFalse(Interpreter::hasError());

        $errorCauser();
        $error = new Error(true, -1);
        $this->assertFalse($error->hasTrace());
        unset($error);
        $this->assertFalse(Interpreter::hasError());
    }

    public function testFunctionError(): void
    {
        require __DIR__ . '/../Resources/Fixtures/error-function.php';

        $error = get_interpreter_error(true);
        $trace = $error->trace();

        foreach ($trace->getRecords() as $step) {
            $this->assertStringMatchesFormat('%s [%s] (%s)', $step->__toString());
        }
    }

    /**
     * @dataProvider provideErrorData
     */
    public function testTrace(\Closure $errorCauser)
    {
        $errorCauser();

        $this->assertTrue(Interpreter::hasError());
        $this->assertInstanceOf(Backtrace::class, $trace = Interpreter::trace());
        $this->assertTrue(Interpreter::hasError());

        $this->assertValidTrace($trace);
    }

    public function testTraceBlacklisting()
    {
        $resolveRecordClasses = function (Backtrace $trace): array {
            return array_map(function (BacktraceRecord $record): string {
                return $record->getClassName();
            }, array_filter($trace->getRecords(), function (BacktraceRecord $record): bool {
                return $record->hasClassName();
            }));
        };

        $trace = Interpreter::trace();
        $this->assertTrue(in_array(self::class, $resolveRecordClasses($trace), true));

        Backtrace::addBlacklistedClasses(self::class);

        $trace = Interpreter::trace();
        $this->assertFalse(in_array(self::class, $resolveRecordClasses($trace), true));

        Backtrace::resetBlacklistedClasses();

        $trace = Interpreter::trace();
        $this->assertTrue(in_array(self::class, $resolveRecordClasses($trace), true));
    }

    public function testReporting()
    {
        $level = error_reporting();
        $reporting = Interpreter::reporting();

        $this->assertInstanceOf(ReportingLevel::class, $reporting);
        $this->assertSame($level, $reporting->level());
        $reporting->level(E_ALL);
        $reporting->level(E_ALL & E_STRICT);
        $reporting->level(E_COMPILE_ERROR);
        $this->assertSame(E_COMPILE_ERROR, $reporting->level());
        $this->assertSame(E_ALL & E_STRICT, $reporting->prior());
        $this->assertSame(E_ALL, $reporting->prior(1));
        $reporting->revert();
        $this->assertSame(E_ALL & E_STRICT, $reporting->level());
        $this->assertSame(E_ALL, $reporting->prior());
        $reporting->level(E_CORE_ERROR);
        $this->assertSame(E_CORE_ERROR, $reporting->level());
        $this->assertSame(E_ALL & E_STRICT, $reporting->prior());
        $this->assertSame(E_ALL, $reporting->prior(1));
        $reporting->revert();
        $this->assertSame(E_ALL & E_STRICT, $reporting->level());
        $this->assertSame(E_ALL, $reporting->prior());
        $reporting->revert();
        $this->assertSame(E_ALL, $reporting->level());
        $this->assertSame($level, $reporting->prior());
        $reporting->revert();
        $this->assertNull($reporting->prior());
        $reporting->revert();
        $this->assertNull($reporting->prior());
    }

    /**
     * @group legacy
     * @group bcl
     */
    public function testDeprecatedNamespace(): void
    {
        $this->assertInstanceOf(Interpreter::class, new \SR\Interpreter\Interpreter());
        $this->assertInstanceOf(Backtrace::class, new \SR\Interpreter\Backtrace\Backtrace());
        $this->assertInstanceOf(BacktraceRecord::class, new \SR\Interpreter\Backtrace\BacktraceRecord([]));
        $this->assertInstanceOf(Error::class, new \SR\Interpreter\Error\Error());
        $this->assertInstanceOf(ReportingLevel::class, new \SR\Interpreter\Reporting\ReportingLevel());
    }

    public function testExtractBacktraceRecordDataWithEmptyArray(): void
    {
        $bt = new BacktraceRecord([]);

        $this->assertEmpty($bt->getArrayData());
        $this->assertNull($bt->getFile());
        $this->assertNull($bt->getLine());
        $this->assertNull($bt->getFuncName());
        $this->assertNull($bt->getFuncReflection());
        $this->assertNull($bt->getFuncCallType());
        $this->assertNull($bt->getClassName());
        $this->assertNull($bt->getObjectInstance());
        $this->assertNull($bt->getObjectReflection());
    }

    private function assertValidTrace(Backtrace $trace): void
    {
        $this->assertIsArray($trace->getRawData());
        $this->assertTrue($trace->hasRawData());
        $this->assertCount(count($trace->getRecords()), $trace);
        $this->assertTrue($trace->hasRecords());

        foreach ($trace->getRecords() as $record) {
            $this->assertValidBacktraceRecord($record);
        }

        foreach ($trace as $record) {
            $this->assertValidBacktraceRecord($record);
        }
    }

    private function assertValidBacktraceRecord(BacktraceRecord $record): void
    {
        if ('object' === $record->getType() || 'class' === $record->getType()) {
            $this->assertStringMatchesFormat('%s [%s] (%s)', (string) $record);
            $this->assertStringMatchesFormat('%s [%s] (%s)', $record->stringify());
            $this->assertInstanceOf(\ReflectionClass::class, $record->getObjectReflection());
            $this->assertTrue($record->hasObjectReflection());
            $this->assertInstanceOf(\ReflectionFunctionAbstract::class, $record->getFuncReflection());
            $this->assertTrue($record->hasFuncReflection());

            if ('->' === $record->getFuncCallType()) {
                $this->assertFalse($record->isFuncCallTypeStatic());
                $this->assertTrue($record->isFuncCallTypeInstance());
            } else {
                $this->assertTrue($record->isFuncCallTypeStatic());
                $this->assertFalse($record->isFuncCallTypeInstance());
            }
        }

        $this->assertTrue($record->hasFuncName());
        $this->assertIsArray($record->getArrayData());
        $this->assertIsArray($record->getArguments());

        if (0 < count($record->getArguments())) {
            $this->assertTrue($record->hasArguments());
        } else {
            $this->assertFalse($record->hasArguments());
        }
    }
}
