<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use SR\Utilities\Context\FileContext;
use SR\Utilities\Context\FileContextInterface;
use SR\Utilities\Interpreter\Backtrace\Backtrace;
use SR\Utilities\Interpreter\Backtrace\BacktraceRecord;
use SR\Utilities\Interpreter\Error\Error;
use SR\Utilities\Interpreter\Interpreter;
use SR\Utilities\Interpreter\Reporting\ReportingLevel;
use SR\Utilities\IO\Buffered\Output\MemoryOutputBuffered;
use SR\Utilities\IO\Buffered\Output\OutputBufferedInterface;
use SR\Utilities\Query\ArrayQuery;
use SR\Utilities\Query\ClassQuery;
use SR\Utilities\Query\EngineQuery;
use SR\Utilities\Query\StringQuery;
use SR\Utilities\Transform\NumberTransform;
use SR\Utilities\Transform\StringTransform;

class_alias(ArrayQuery::class, 'SR\Utilities\ArrayQuery');
class_alias(ArrayQuery::class, 'SR\Util\Info\ArrayInfo');

class_alias(ClassQuery::class, 'SR\Utilities\ClassQuery');
class_alias(ClassQuery::class, 'SR\Util\Info\ClassInfo');

class_alias(EngineQuery::class, 'SR\Utilities\EngineQuery');
class_alias(EngineQuery::class, 'SR\Util\Info\EngineInfo');

class_alias(StringQuery::class, 'SR\Utilities\StringQuery');
class_alias(StringQuery::class, 'SR\Util\Info\StringInfo');

class_alias(FileContext::class, 'SR\Util\Context\FileContext');
class_alias(FileContextInterface::class, 'SR\Util\Context\FileContextInterface');

class_alias(NumberTransform::class, 'SR\Util\Transform\NumberTransform');
class_alias(StringTransform::class, 'SR\Util\Transform\StringTransform');

class_alias(Interpreter::class, 'SR\Interpreter\Interpreter');
class_alias(Backtrace::class, 'SR\Interpreter\Backtrace\Backtrace');
class_alias(BacktraceRecord::class, 'SR\Interpreter\Backtrace\BacktraceRecord');
class_alias(Error::class, 'SR\Interpreter\Error\Error');
class_alias(ReportingLevel::class, 'SR\Interpreter\Reporting\ReportingLevel');

class_alias(MemoryOutputBuffered::class, 'SR\Output\Buffered\MemoryOutputBuffered');
class_alias(OutputBufferedInterface::class, 'SR\Output\Buffered\OutputBufferedInterface');
