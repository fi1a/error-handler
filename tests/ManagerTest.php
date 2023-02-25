<?php

declare(strict_types=1);

namespace Fi1a\Unit\ErrorHandler;

use Fi1a\ErrorHandler\Handlers\HandlerCollection;
use Fi1a\ErrorHandler\Handlers\PrettyPageHandler;
use Fi1a\ErrorHandler\Manager;
use Fi1a\ErrorHandler\ManagerInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

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
}
