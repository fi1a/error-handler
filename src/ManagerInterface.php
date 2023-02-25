<?php

declare(strict_types=1);

namespace Fi1a\ErrorHandler;

use Fi1a\ErrorHandler\Handlers\HandlerCollectionInterface;
use Fi1a\ErrorHandler\Handlers\HandlerInterface;
use Throwable;

/**
 * Менеджер
 */
interface ManagerInterface
{
    /**
     * Добавить обработчик
     *
     * @return $this
     */
    public function pushHandler(HandlerInterface $handler);

    /**
     * Возвращает коллекцию обработчиков
     */
    public function getHandlers(): HandlerCollectionInterface;

    /**
     * Устанавливает коллекцию обработчиков
     *
     * @return $this
     */
    public function setHandlers(HandlerCollectionInterface $handlerCollection);

    /**
     * Зарегистрировать обработку ошибок и исключений
     *
     * @return $this
     */
    public function register();

    /**
     * Удалить регистрацию обработки ошибок и исключений
     *
     * @return $this
     */
    public function unregister();

    /**
     * Зарегистрирована обработка ошибок или исключений
     */
    public function isRegister(): bool;

    /**
     * Обработать исключение
     */
    public function handleException(Throwable $exception): void;
}
