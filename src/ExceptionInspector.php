<?php

declare(strict_types=1);

namespace Fi1a\ErrorHandler;

use ErrorException;
use Throwable;

use const PHP_EOL;

/**
 * Возвращает данные исключения
 */
class ExceptionInspector implements InspectorInterface
{
    /**
     * @var Throwable
     */
    protected $exception;

    /**
     * @var int
     */
    protected $countLineOfCode;

    public function __construct(Throwable $exception, int $countLineOfCode = 10)
    {
        $this->exception = $exception;
        $this->countLineOfCode = $countLineOfCode;
    }

    /**
     * @inheritDoc
     */
    public function getMessage(): string
    {
        return $this->exception->getMessage();
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return get_class($this->exception);
    }

    /**
     * @inheritDoc
     */
    public function getCode(): string
    {
        $code = $this->exception->getCode();
        if ($this->exception instanceof ErrorException) {
            $constants = get_defined_constants(true);
            if (array_key_exists('Core', $constants)) {
                foreach ($constants['Core'] as $constant => $value) {
                    if (mb_substr($constant, 0, 2) === 'E_' && $value === $this->exception->getSeverity()) {
                        return $constant;
                    }
                }
            }

            return 'E_UNKNOWN';
        }

        return (string) $code;
    }

    /**
     * @inheritDoc
     */
    public function getBacktrace(): array
    {
        $backtrace = [];

        $previous = $this->exception;
        do {
            $backtrace[] = $this->getBacktraceException($previous);
        } while ($previous = $previous->getPrevious());

        /**
         * @var array{
         *     class: class-string|null,
         *     type: string|null,
         *     function: string|null,
         *     file: string|null,
         *     line: int|null
         * } $item
         */
        foreach ($this->exception->getTrace() as $item) {
            $backtrace[] = $this->getBacktraceItem($item);
        }

        return $backtrace;
    }

    /**
     * Возвращает элемент трассировки для исключения
     *
     * @psalm-return array{code: string, file: string|null, line: int, name: string, start: int<2, max>}
     */
    protected function getBacktraceException(Throwable $exception): array
    {
        return $this->getBacktraceItem([
            'class' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'type' => null,
            'function' => null,
        ]);
    }

    /**
     * Возвращает элемент трассировки
     *
     * @param array{
     *     class: class-string|null,
     *     type: string|null,
     *     function: string|null,
     *     file: string|null,
     *     line: int|null
     * } $item
     *
     * @psalm-return array{code: string, file: string|null, line: int, name: string, start: int<2, max>}
     */
    protected function getBacktraceItem(array $item): array
    {
        $name = '';
        if (isset($item['class'])) {
            $name .= $item['class'];
        }
        if (isset($item['type'])) {
            $name .= $item['type'];
        }
        if (isset($item['function'])) {
            $name .= $item['function'];
        }

        $start = 1;
        $code = '';
        if (isset($item['file']) && $item['file'] && is_file($item['file'])) {
            if (isset($item['line'])) {
                $start = $item['line'] - $this->countLineOfCode;
                if ($start <= 0) {
                    $start = 1;
                }
            }
            $code = file_get_contents($item['file']);
            $code = implode(PHP_EOL, array_slice(explode(PHP_EOL, $code), $start, 2 * $this->countLineOfCode));
        }

        return [
            'name' => $name,
            'file' => $item['file'] ?? '',
            'line' => $item['line'] ?? 0,
            'start' => $start + 1,
            'code' => $code,
        ];
    }

    /**
     * @inheritDoc
     */
    public function getPrevious(): ?InspectorInterface
    {
        if (!$this->exception->getPrevious()) {
            return null;
        }

        return new ExceptionInspector($this->exception->getPrevious());
    }
}
