<?php

declare(strict_types=1);

namespace Fi1a\ErrorHandler\Handlers;

use Fi1a\ErrorHandler\InspectorInterface;

/**
 * Обработчик
 */
interface HandlerInterface
{
    /**
     * Обработчик
     */
    public function handle(InspectorInterface $inspector): void;
}
