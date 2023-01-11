<?php

declare(strict_types=1);

namespace Fi1a\Log;

use DateTime;

/**
 * Запись лога
 */
interface RecordInterface
{
    /**
     * @param mixed[] $values
     * @param mixed[] $context
     */
    public function __construct(
        DateTime $datetime,
        LevelInterface $level,
        string $channel,
        string $message,
        array $values = [],
        array $context = []
    );
}
