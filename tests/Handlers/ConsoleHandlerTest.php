<?php

declare(strict_types=1);

namespace Fi1a\Unit\ErrorHandler\Handlers;

use Fi1a\Console\IO\ConsoleOutput;
use Fi1a\Console\IO\ConsoleOutputInterface;
use Fi1a\Console\IO\Formatter;
use Fi1a\Console\IO\Stream;
use Fi1a\DI\Builder;
use Fi1a\ErrorHandler\Handlers\ConsoleHandler;

/**
 * Console обработчик
 *
 * @runInSeparateProcess
 * @backupGlobals false
 */
class ConsoleHandlerTest extends HandlerTestCase
{
    /**
     * Обработчик
     */
    public function testHandle(): void
    {
        $stream = new Stream('php://memory');
        $output = new ConsoleOutput(new Formatter());
        $output->setStream($stream);
        di()->config()->addDefinition(
            Builder::build(ConsoleOutputInterface::class)
                ->defineObject($output)
                ->getDefinition()
        );
        $handler = new ConsoleHandler();
        $handler->handle($this->getExceptionInspector());
        $stream->seek(0);
        $this->assertIsString(stream_get_contents($stream->getStream()));
    }
}
