<?php

declare(strict_types=1);

namespace Fi1a\Log\Handlers;

use Fi1a\Log\LevelInterface;
use Fi1a\Log\RecordInterface;

/**
 * Обработчик логирования
 */
interface HandlerInterface
{
    /**
     * Метод обработчик
     */
    public function handle(RecordInterface $record): bool;

    /**
     * Возвращает уровень логирования
     */
    public function getLevel(): LevelInterface;

    /**
     * Устанавливает уровень логирования
     *
     * @return $this
     */
    public function setLevel(LevelInterface $level);

    /**
     * По уровню логирования определяет будет ли выполнено оно
     */
    public function isHandling(LevelInterface $level): bool;
}
