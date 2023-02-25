<?php

declare(strict_types=1);

namespace Fi1a\Unit\ErrorHandler;

use ErrorException;
use Fi1a\ErrorHandler\ExceptionInspector;
use Fi1a\ErrorHandler\InspectorInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

use const E_COMPILE_ERROR;

/**
 * Возвращает данные исключения
 */
class ExceptionInspectorTest extends TestCase
{
    protected function getExceptionInspector(): InspectorInterface
    {
        return new ExceptionInspector(new InvalidArgumentException('Message', 100, new InvalidArgumentException()));
    }

    /**
     * Код
     */
    public function testCode(): void
    {
        $inspector = $this->getExceptionInspector();
        $this->assertEquals('100', $inspector->getCode());
    }

    /**
     * Код
     */
    public function testCodeBySeverity(): void
    {
        $inspector = new ExceptionInspector(new ErrorException('Message', 100, E_COMPILE_ERROR));
        $this->assertEquals('E_COMPILE_ERROR', $inspector->getCode());
    }

    /**
     * Код
     */
    public function testCodeBySeverityUnknown(): void
    {
        $inspector = new ExceptionInspector(new ErrorException('Message', 100, 0));
        $this->assertEquals('E_UNKNOWN', $inspector->getCode());
    }

    /**
     * Возврашает стек вызовов
     */
    public function testBacktrace(): void
    {
        $inspector = $this->getExceptionInspector();
        $this->assertIsArray($inspector->getBacktrace());
    }

    /**
     * Возврашает стек вызовов
     */
    public function testBacktraceCountLines(): void
    {
        $inspector = new ExceptionInspector(new ErrorException('Message', 100, E_COMPILE_ERROR, __FILE__, 10), 1000);
        $this->assertIsArray($inspector->getBacktrace());
    }

    /**
     * Возвращает заголовок
     */
    public function testMessage(): void
    {
        $inspector = $this->getExceptionInspector();
        $this->assertEquals('Message', $inspector->getMessage());
    }

    /**
     * Возвращает название
     */
    public function testName(): void
    {
        $inspector = $this->getExceptionInspector();
        $this->assertEquals(InvalidArgumentException::class, $inspector->getName());
    }

    /**
     * Возвращает предыдущий узел
     */
    public function testPrevious(): void
    {
        $inspector = $this->getExceptionInspector();
        $this->assertInstanceOf(InspectorInterface::class, $inspector->getPrevious());
    }

    /**
     * Возвращает предыдущий узел
     */
    public function testNoPrevious(): void
    {
        $inspector = new ExceptionInspector(new ErrorException('Message', 100, 0));
        $this->assertNull($inspector->getPrevious());
    }
}
