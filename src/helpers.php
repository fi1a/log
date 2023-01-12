<?php

declare(strict_types=1);

use Fi1a\Log\ChannelsInterface;
use Fi1a\Log\LoggerInterface;

/**
 * Возвращает каналы логгера
 *
 * @return ChannelsInterface|LoggerInterface|false
 */
function logger(?string $channel = null)
{
    /** @var ChannelsInterface $channels */
    $channels = di()->get(ChannelsInterface::class);
    if ($channel !== null) {
        return $channels->channel($channel);
    }

    return $channels;
}
