<?php

declare(strict_types=1);

namespace Fi1a\Log;

/**
 * Каналы логирования
 */
interface ChannelsInterface
{
    /**
     * Возвращает канал логирования с определенным именем
     *
     * @return LoggerInterface|false
     */
    public function channel(?string $channel = null);

    /**
     * Контекстная информация
     *
     * @param mixed[] $context
     */
    public function withContext(array $context): bool;

    /**
     * Добавить канал
     */
    public function addChannel(LoggerInterface $logger): bool;

    /**
     * Проверяет наличие канала
     */
    public function hasChannel(string $channel): bool;

    /**
     * Удаляет канал
     */
    public function deleteChannel(string $channel): bool;
}
