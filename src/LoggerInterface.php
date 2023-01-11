<?php

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
     */
    public function emergency(string $message, array $values = []): bool;

    /**
     * Ошибка. Необходимо принять меры немедленно.
     *
     * Пример: база данных не доступна, ...
     *
     * @param mixed[] $values
     */
    public function alert(string $message, array $values = []): bool;

    /**
     * Критическая ошибка
     *
     * Пример: неожиданное исключение.
     *
     * @param mixed[] $values
     */
    public function critical(string $message, array $values = []): bool;

    /**
     * Ошибка которая не требует немедленных действий, но должна быть записана
     *
     * @param mixed[] $values
     */
    public function error(string $message, array $values = []): bool;

    /**
     * Предупреждение
     *
     * Пример: отсутствие файла и т.д.
     *
     * @param mixed[] $values
     */
    public function warning(string $message, array $values = []): bool;

    /**
     * Все нормально, но событие значимое
     *
     * @param mixed[] $values
     */
    public function notice(string $message, array $values = []): bool;

    /**
     * Какое либо событие
     *
     * @param mixed[] $values
     */
    public function info(string $message, array $values = []): bool;

    /**
     * Отладочная информация
     *
     * @param mixed[] $values
     */
    public function debug(string $message, array $values = []): bool;

    /**
     * Логирование с определенным уровнем
     *
     * @param mixed[]  $values
     */
    public function log(LevelInterface $level, string $message, array $values = []): bool;

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
     * @return HandlerInterface[]
     */
    public function getHandlers(): array;
}
