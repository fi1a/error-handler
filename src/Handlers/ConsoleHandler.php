<?php

declare(strict_types=1);

namespace Fi1a\ErrorHandler\Handlers;

use Fi1a\Console\IO\ConsoleOutputInterface;
use Fi1a\Console\IO\Style\ANSIColor;
use Fi1a\Console\IO\Style\ANSIStyle;
use Fi1a\ErrorHandler\InspectorInterface;

/**
 * Console обработчик
 */
class ConsoleHandler implements HandlerInterface
{
    /**
     * @var ConsoleOutputInterface
     */
    protected $output;

    public function __construct(?ConsoleOutputInterface $output = null)
    {
        if ($output === null) {
            /** @var ConsoleOutputInterface $output */
            $output = di()->get(ConsoleOutputInterface::class);
        }
        $this->output = $output;
    }

    /**
     * @inheritDoc
     */
    public function handle(InspectorInterface $inspector): void
    {
        $this->output->write($inspector->getName(), [], new ANSIStyle(ANSIColor::WHITE, ANSIColor::RED));
        $this->output->write(' (' . $inspector->getCode() . ')', [], new ANSIStyle(ANSIColor::DARK_GRAY));
        $this->output->writeln();
        $this->output->write($inspector->getName());
        $this->output->writeln();
        $previous = $inspector->getPrevious();
        if ($previous) {
            $this->output->writeln();
            $this->output->writeln('Предыдущие исключения', [], new ANSIStyle(ANSIColor::LIGHT_BLUE));
            $this->output->writeln();
            while ($previous) {
                $this->output->write($previous->getMessage());
                $this->output->write(' - ');
                $this->output->write($previous->getName(), [], new ANSIStyle(ANSIColor::RED));
                $this->output->write(' (' . $inspector->getCode() . ')', [], new ANSIStyle(ANSIColor::DARK_GRAY));
                $this->output->writeln();
                $previous = $previous->getPrevious();
            }
            $this->output->writeln();
        }

        $backtrace = $inspector->getBacktrace();
        $number = count($backtrace);

        $this->output->writeln('Стек вызова', [], new ANSIStyle(ANSIColor::LIGHT_BLUE));
        $this->output->writeln();

        foreach ($backtrace as $item) {
            $number--;

            $this->output->write((string) $number . '. ');
            $this->output->writeln($item['name']);

            if ($item['file']) {
                $this->output->writeln(
                    $item['file'] . ($item['line'] ? ':' . $item['line'] : ''),
                    [],
                    new ANSIStyle(ANSIColor::DARK_GRAY)
                );
            }

            if ($number === count($backtrace) - 1 && $item['code']) {
                $this->output->writeln();
                $this->output->write($item['code'], [], new ANSIStyle(ANSIColor::LIGHT_YELLOW));
            }

            $this->output->writeln();
            $this->output->writeln();
        }
    }
}
