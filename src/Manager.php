<?php

declare(strict_types=1);

namespace Fi1a\ErrorHandler;

use ErrorException;
use Fi1a\ErrorHandler\Handlers\HandlerCollection;
use Fi1a\ErrorHandler\Handlers\HandlerCollectionInterface;
use Fi1a\ErrorHandler\Handlers\HandlerInterface;
use Throwable;

use const E_ALL;

/**
 * Менеджер
 */
class Manager implements ManagerInterface
{
    /**
     * @var HandlerCollectionInterface
     */
    protected $handlers;

    /**
     * @var bool
     */
    protected $isRegister = false;

    public function __construct()
    {
        $this->setHandlers(new HandlerCollection());
    }

    /**
     * @inheritDoc
     */
    public function pushHandler(HandlerInterface $handler)
    {
        $this->handlers->add($handler);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getHandlers(): HandlerCollectionInterface
    {
        return $this->handlers;
    }

    /**
     * @inheritDoc
     */
    public function setHandlers(HandlerCollectionInterface $handlerCollection)
    {
        $this->handlers = $handlerCollection;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function register()
    {
        if ($this->isRegister()) {
            return $this;
        }
        $this->isRegister = true;
        set_exception_handler([$this, 'handleException']);
        set_error_handler([$this, 'handleError'], E_ALL);
        register_shutdown_function([$this, 'handleShutdown']);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function unregister()
    {
        if (!$this->isRegister()) {
            return $this;
        }
        $this->isRegister = false;
        restore_exception_handler();
        restore_error_handler();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isRegister(): bool
    {
        return $this->isRegister;
    }

    /**
     * @inheritDoc
     */
    public function handleException(Throwable $exception): void
    {
        $inspector = new ExceptionInspector($exception);

        /** @var HandlerInterface $handler */
        foreach ($this->handlers as $handler) {
            $handler->handle($inspector);
        }

        $this->terminate();
    }

    /**
     * @inheritDoc
     */
    public function handleError(int $number, string $message, string $file, int $line): bool
    {
        if (error_reporting() === 0) {
            return false;
        }
        if (!(error_reporting() & $number)) {
            return false;
        }

        $this->handleException(new ErrorException($message, $number, $number, $file, $line));

        return true;
    }

    /**
     * @inheritDoc
     */
    public function handleShutdown(): void
    {
        $error = $this->getLastError();
        if (!isset($error)) {
            return;
        }

        $this->handleError($error['type'], $error['message'], $error['file'], $error['line']);
    }

    /**
     * Возвращает последнюю ошибку
     *
     * @return array{type: int, message: string, file: string, line: int}|null
     *
     * @codeCoverageIgnore
     */
    protected function getLastError(): ?array
    {
        return error_get_last();
    }

    /**
     * Завершает работу
     *
     * @codeCoverageIgnore
     */
    protected function terminate(): void
    {
        exit(1);
    }
}
