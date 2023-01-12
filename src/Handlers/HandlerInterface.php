<?php

declare(strict_types=1);

namespace Fi1a\Log\Handlers;

use Fi1a\Log\LevelInterface;
use Fi1a\Log\Record;

/**
 * Обработчик логирования
 */
interface HandlerInterface
{
    /**
     * Метод обработчик
     */
    public function handle(Record $record): bool;

    /**
     * Возвращает уровень логирования
     */
    public function getLevel(): LevelInterface;

    /**
     * Устанавливает уровень логирования
     *
     * @param int|string|LevelInterface $level
     *
     * @return $this
     */
    public function setLevel($level);

    /**
     * По уровню логирования определяет будет ли выполнено оно
     */
    public function isHandling(LevelInterface $level): bool;

    /**
     * Закрывает обработчик
     */
    public function close(): void;
}
