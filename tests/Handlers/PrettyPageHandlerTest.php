<?php

declare(strict_types=1);

namespace Fi1a\Unit\ErrorHandler\Handlers;

use Fi1a\ErrorHandler\ExceptionInspector;
use Fi1a\ErrorHandler\Handlers\PrettyPageHandler;
use Fi1a\ErrorHandler\InspectorInterface;
use Fi1a\Http\Http;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Html обработчик
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class PrettyPageHandlerTest extends TestCase
{
    protected function getExceptionInspector(): InspectorInterface
    {
        return new ExceptionInspector(new InvalidArgumentException('Message', 100, new InvalidArgumentException()));
    }

    /**
     * Обработчик
     */
    public function testHandle(): void
    {
        request(Http::createRequestWithGlobals(['foo' => 'bar'], [], ['foo' => 'bar'], [], []));
        ob_start();
        $handler = new PrettyPageHandler(['$_GET' => 'foo',]);
        $handler->handle($this->getExceptionInspector());
        $this->assertIsString(ob_get_contents());
        ob_clean();
    }
}
