<?php

declare(strict_types=1);

namespace Fi1a\Log;

use Fi1a\Log\Handlers\HandlerInterface;

/**
 * Логгер
 */
interface LoggerInterface
{
    /**
     * Система не работает
     *
     * @param mixed[] $values
     * @param mixed[] $context
     */
    public function emergency(string $message, array $values = [], array $context = []): bool;

    /**
     * Ошибка. Необходимо принять меры немедленно.
     *
     * Пример: база данных не доступна, ...
     *
     * @param mixed[] $values
     * @param mixed[] $context
     */
    public function alert(string $message, array $values = [], array $context = []): bool;

    /**
     * Критическая ошибка
     *
     * Пример: неожиданное исключение.
     *
     * @param mixed[] $values
     * @param mixed[] $context
     */
    public function critical(string $message, array $values = [], array $context = []): bool;

    /**
     * Ошибка которая не требует немедленных действий, но должна быть записана
     *
     * @param mixed[] $values
     * @param mixed[] $context
     */
    public function error(string $message, array $values = [], array $context = []): bool;

    /**
     * Предупреждение
     *
     * Пример: отсутствие файла и т.д.
     *
     * @param mixed[] $values
     * @param mixed[] $context
     */
    public function warning(string $message, array $values = [], array $context = []): bool;

    /**
     * Все нормально, но событие значимое
     *
     * @param mixed[] $values
     * @param mixed[] $context
     */
    public function notice(string $message, array $values = [], array $context = []): bool;

    /**
     * Какое либо событие
     *
     * @param mixed[] $values
     * @param mixed[] $context
     */
    public function info(string $message, array $values = [], array $context = []): bool;

    /**
     * Отладочная информация
     *
     * @param mixed[] $values
     * @param mixed[] $context
     */
    public function debug(string $message, array $values = [], array $context = []): bool;

    /**
     * Логирование с определенным уровнем
     *
     * @param LevelInterface|int|string $level
     * @param mixed[] $values
     * @param mixed[] $context
     */
    public function log($level, string $message, array $values = [], array $context = []): bool;

    /**
     * Контекстная информация
     *
     * @param mixed[] $context
     */
    public function withContext(array $context): bool;

    /**
     * Добавить обработчик
     */
    public function addHandler(HandlerInterface $handler): bool;

    /**
     * Установить обработчики
     *
     * @param HandlerInterface[] $handlers
     */
    public function setHandlers(array $handlers): bool;

    /**
     * Возвращает обработчики
     *
     * @return HandlerInterface[]
     */
    public function getHandlers(): array;

    /**
     * Возвращает название канала
     */
    public function getChannel(): string;
}
