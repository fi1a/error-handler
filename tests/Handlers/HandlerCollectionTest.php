<?php

declare(strict_types=1);

namespace Fi1a\Unit\ErrorHandler\Handlers;

use Fi1a\ErrorHandler\Handlers\HandlerCollection;
use Fi1a\ErrorHandler\Handlers\PrettyPageHandler;
use PHPUnit\Framework\TestCase;

/**
 * Коллекция обработчиков
 */
class HandlerCollectionTest extends TestCase
{
    /**
     * Коллекция обработчиков
     */
    public function testCollection(): void
    {
        $collection = new HandlerCollection();
        $this->assertCount(0, $collection);
        $collection[] = new PrettyPageHandler();
        $collection[] = new PrettyPageHandler();
        $this->assertCount(2, $collection);
    }
}
