<?php

declare(strict_types=1);

namespace Fi1a\ErrorHandler;

use Fi1a\ErrorHandler\Handlers\HandlerCollection;
use Fi1a\ErrorHandler\Handlers\HandlerCollectionInterface;
use Fi1a\ErrorHandler\Handlers\HandlerInterface;
use Throwable;

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
     * Завершает работу
     *
     * @codeCoverageIgnore
     */
    protected function terminate(): void
    {
        exit(1);
    }
}
