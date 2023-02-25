<?php

declare(strict_types=1);

namespace Fi1a\ErrorHandler\Handlers;

use Fi1a\Collection\Collection;

/**
 * Коллекция обработчиков
 */
class HandlerCollection extends Collection implements HandlerCollectionInterface
{
    /**
     * @param HandlerInterface[]|null $data
     */
    public function __construct(?array $data = null)
    {
        parent::__construct(HandlerInterface::class, $data);
    }
}
