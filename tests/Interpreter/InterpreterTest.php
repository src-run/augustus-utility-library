<?php

/*
 * This file is part of the `src-run/augustus-silencer-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Interpreter\Tests;

use PHPUnit\Framework\TestCase;
use SR\Interpreter\Interpreter;
use SR\Interpreter\Model\Error\ErrorModel;
use SR\Interpreter\Model\Error\ReportingModel;
use SR\Interpreter\Model\Error\Trace\BacktraceModel;
use SR\Interpreter\Model\Error\Trace\Record\BacktraceRecordModel;
use SR\Interpreter\Model\Error\Trace\Record\ClassTraceStepModel;
use SR\Interpreter\Model\Error\Trace\Record\FunctionTraceStepModel;
use SR\Interpreter\Model\Error\Trace\Record\ObjectTraceStepModel;

/**
 * @covers \SR\Interpreter\Interpreter
 * @covers \SR\Interpreter\Model\Error\ErrorModel
 * @covers \SR\Interpreter\Model\Error\Trace\BacktraceModel
 * @covers \SR\Interpreter\Model\Error\Trace\Record\BacktraceRecordModel
 * @covers \SR\Interpreter\Model\Error\ReportingModel
 */
class InterpreterTest extends TestCase
{
    public static function provideErrorData(): \Iterator
    {
        yield ['file_get_contents(%s/foo/bar/baz.ext): failed to open stream: No such file or directory', function () {
            @file_get_contents(sprintf('%s/foo/bar/baz.ext', sys_get_temp_dir()));
        }];
        yield ['unlink(%s/foo/bar/baz.ext): No such file or directory', function () {
            @unlink(sprintf('%s/foo/bar/baz.ext', sys_get_temp_dir()));
        }];
    }

    /**
     * @dataProvider provideErrorData
     *
     * @param string   $expectedMessageFormat
     * @param \Closure $errorCauser
     */
    public function testError(string $expectedMessageFormat, \Closure $errorCauser)
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

        $this->assertInternalType('int', $error->type());
        $this->assertInstanceOf(\SplFileInfo::class, $error->file());
        $this->assertInternalType('int', $error->line());
        $this->assertInstanceOf(BacktraceModel::class, $error->trace());
        $this->assertTrue($error->hasFile());
        $this->assertTrue($error->hasTrace());
        $this->assertTrue($error->isReal());
        $this->assertFalse($error->isMock());
        unset($error);
        $this->assertFalse(Interpreter::hasError());

        $errorCauser();
        $error = new ErrorModel(true, -1);
        $this->assertFalse($error->hasTrace());
        unset($error);
        $this->assertFalse(Interpreter::hasError());
    }

    public function testFunctionError(): void
    {
        require __DIR__.'/../Resources/Fixtures/error-function.php';

        $error = get_interpreter_error(true);
        $trace = $error->trace();

        foreach ($trace->getRecords() as $step) {
            $this->assertStringMatchesFormat('%s [%s] (%s)', $step->__toString());
        }
    }

    /**
     * @dataProvider provideErrorData
     *
     * @param string   $expectedMessageFormat
     * @param \Closure $errorCauser
     */
    public function testTrace(string $expectedMessageFormat, \Closure $errorCauser)
    {
        $errorCauser();

        $this->assertInstanceOf(BacktraceModel::class, $trace = Interpreter::trace());
        $this->assertTrue(Interpreter::hasError());

        $this->assertValidTrace($trace);
    }

    public function testReporting()
    {
        $level = error_reporting();
        $reporting = Interpreter::reporting();

        $this->assertInstanceOf(ReportingModel::class, $reporting);
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
     * @param BacktraceModel $trace
     */
    private function assertValidTrace(BacktraceModel $trace): void
    {
        $this->assertInternalType('array', $trace->getArrayData());
        $this->assertTrue($trace->hasArrayData());
        $this->assertCount(count($trace->getRecords()), $trace);
        $this->assertTrue($trace->hasRecords());

        foreach ($trace->getRecords() as $record) {
            $this->assertValidBacktraceRecord($record);
        }

        foreach ($trace as $record) {
            $this->assertValidBacktraceRecord($record);
        }
    }

    /**
     * @param BacktraceRecordModel $record
     */
    private function assertValidBacktraceRecord(BacktraceRecordModel $record): void
    {
        if ($record->getType() === 'object' || $record->getType() === 'class') {
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
        $this->assertInternalType('array', $record->getArrayData());
        $this->assertInternalType('array', $record->getArguments());

        if (0 < count($record->getArguments())) {
            $this->assertTrue($record->hasArguments());
        } else {
            $this->assertFalse($record->hasArguments());
        }
    }
}
