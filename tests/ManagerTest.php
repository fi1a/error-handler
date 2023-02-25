<?php

declare(strict_types=1);

namespace Fi1a\Unit\ErrorHandler;

use Fi1a\ErrorHandler\Handlers\HandlerCollection;
use Fi1a\ErrorHandler\Handlers\PrettyPageHandler;
use Fi1a\ErrorHandler\Manager;
use Fi1a\ErrorHandler\ManagerInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

use const E_COMPILE_ERROR;
use const E_NOTICE;

/**
 * Менеджер
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class ManagerTest extends TestCase
{
    public function getManager(): ManagerInterface
    {
        return new Manager();
    }

    /**
     * Методы добавления обработчиков
     */
    public function testHandlers(): void
    {
        $manager = $this->getManager();
        $this->assertCount(0, $manager->getHandlers());
        $manager->pushHandler(new PrettyPageHandler());
        $this->assertCount(1, $manager->getHandlers());
        $manager->setHandlers(new HandlerCollection());
        $this->assertCount(0, $manager->getHandlers());
    }

    /**
     * Регистрация обработки ошибок
     */
    public function testRegister(): void
    {
        $manager = $this->getManager();
        $this->assertFalse($manager->isRegister());
        $manager->register();
        $this->assertTrue($manager->isRegister());
        $manager->register();
        $this->assertTrue($manager->isRegister());
        $manager->unregister();
        $this->assertFalse($manager->isRegister());
        $manager->unregister();
        $this->assertFalse($manager->isRegister());
    }

    /**
     * Обработка исключения
     */
    public function testHandleException(): void
    {
        $manager = $this->getMockBuilder(Manager::class)
            ->onlyMethods(['terminate'])
            ->getMock();

        $manager->expects($this->once())->method('terminate');

        ob_start();
        $manager->pushHandler(new PrettyPageHandler());

        $manager->handleException(new InvalidArgumentException());
        ob_clean();
    }

    /**
     * Обработка ошибки
     */
    public function testHandleError(): void
    {
        $manager = $this->getMockBuilder(Manager::class)
            ->onlyMethods(['terminate'])
            ->getMock();

        $manager->expects($this->once())->method('terminate');

        ob_start();
        $manager->pushHandler(new PrettyPageHandler());

        $manager->handleError(E_COMPILE_ERROR, 'Message', __FILE__, 86);
        ob_clean();
    }

    /**
     * Обработка ошибки
     */
    public function testHandleErrorLevel(): void
    {
        $errorReporting = error_reporting();
        error_reporting(E_NOTICE);
        $manager = $this->getMockBuilder(Manager::class)
            ->onlyMethods(['terminate'])
            ->getMock();

        $manager->expects($this->never())->method('terminate');

        $manager->pushHandler(new PrettyPageHandler());

        $manager->handleError(E_COMPILE_ERROR, 'Message', __FILE__, 86);

        error_reporting($errorReporting);
    }

    /**
     * Обработка ошибки
     */
    public function testHandleErrorDisabled(): void
    {
        $errorReporting = error_reporting();
        error_reporting(0);
        $manager = $this->getMockBuilder(Manager::class)
            ->onlyMethods(['terminate'])
            ->getMock();

        $manager->expects($this->never())->method('terminate');

        $manager->pushHandler(new PrettyPageHandler());

        $manager->handleError(E_COMPILE_ERROR, 'Message', __FILE__, 86);

        error_reporting($errorReporting);
    }

    /**
     * Метод выполняемый по завершению работы скрипта
     */
    public function testHandleShutdown(): void
    {
        $manager = $this->getMockBuilder(Manager::class)
            ->onlyMethods(['terminate', 'getLastError'])
            ->getMock();

        $manager->expects($this->once())->method('terminate');
        $manager->expects($this->once())
            ->method('getLastError')
            ->willReturn([
                'type' => E_NOTICE,
                'message' => 'message',
                'file' => __FILE__,
                'line' => 149,
            ]);

        ob_start();
        $manager->pushHandler(new PrettyPageHandler());

        $manager->handleShutdown();
        ob_clean();
    }

    /**
     * Метод выполняемый по завершению работы скрипта
     */
    public function testHandleShutdownEmptyError(): void
    {
        $manager = $this->getMockBuilder(Manager::class)
            ->onlyMethods(['terminate', 'getLastError'])
            ->getMock();

        $manager->expects($this->never())->method('terminate');
        $manager->expects($this->once())
            ->method('getLastError')
            ->willReturn(null);

        $manager->pushHandler(new PrettyPageHandler());

        $manager->handleShutdown();
    }
}
