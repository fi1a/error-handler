<?php

declare(strict_types=1);

namespace Fi1a\ErrorHandler;

/**
 * Возвращает данные исключения
 */
interface InspectorInterface
{
    /**
     * Возвращает заголовок
     */
    public function getMessage(): string;

    /**
     * Возвращает название
     */
    public function getName(): string;

    /**
     * Возвращает код
     */
    public function getCode(): string;

    /**
     * Возвращает предыдущий узел
     */
    public function getPrevious(): ?InspectorInterface;

    /**
     * Возврашает стек вызовов
     *
     * @psalm-return non-empty-list<array{code: string, file: string|null, line: int, name: string, start: int<2, max>}>
     */
    public function getBacktrace(): array;
}
