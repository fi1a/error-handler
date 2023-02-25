<?php

declare(strict_types=1);

namespace Fi1a\Unit\ErrorHandler\Handlers;

use Fi1a\ErrorHandler\ExceptionInspector;
use Fi1a\ErrorHandler\InspectorInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * TestCase для обработчиков
 */
class HandlerTestCase extends TestCase
{
    protected function getExceptionInspector(): InspectorInterface
    {
        return new ExceptionInspector(new InvalidArgumentException('Message', 100, new InvalidArgumentException()));
    }
}
