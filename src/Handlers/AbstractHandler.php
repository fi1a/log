<?php

declare(strict_types=1);

namespace Fi1a\Log\Handlers;

use Fi1a\Log\Formatters\FormatterInterface;
use Fi1a\Log\Formatters\TextFormatter;
use Fi1a\Log\Level;
use Fi1a\Log\LevelInterface;
use Fi1a\Log\Record;

/**
 * Абстрактный обработчик логирования
 */
abstract class AbstractHandler implements HandlerInterface
{
    /**
     * @var LevelInterface
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private $level;

    /**
     * @var FormatterInterface
     */
    private $formatter;

    /**
     * @param int|string|LevelInterface $level
     */
    public function __construct($level = LevelInterface::DEBUG, ?FormatterInterface $formatter = null)
    {
        $this->setLevel($level);
        if ($formatter === null) {
            $formatter = new TextFormatter();
        }
        $this->formatter = $formatter;
    }

    /**
     * Метод логирования
     */
    abstract protected function write(Record $record): bool;

    /**
     * @inheritDoc
     */
    public function handle(Record $record): bool
    {
        if (!$this->isHandling($record->level)) {
            return false;
        }
        $record->formatted = $this->formatter->format($record);

        return $this->write($record);
    }

    /**
     * @inheritDoc
     */
    public function getLevel(): LevelInterface
    {
        return $this->level;
    }

    /**
     * @inheritDoc
     */
    public function setLevel($level)
    {
        if (!($level instanceof LevelInterface)) {
            $level = Level::from($level);
        }
        $this->level = $level;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isHandling(LevelInterface $level): bool
    {
        return $this->level->includes($level);
    }
}
