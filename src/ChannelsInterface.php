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
     */
    public function channel(string $channel): LoggerInterface;

    /**
     * Контекстная информация
     *
     * @param mixed[] $context
     */
    public function withContext(array $context): bool;
}
