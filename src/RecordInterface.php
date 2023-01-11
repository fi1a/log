<?php

namespace Fi1a\Log;

use DateTime;

/**
 * Запись лога
 */
interface RecordInterface
{
    public function __construct(
        DateTime $datetime,
        LevelInterface $level,
        string $message,
        array $context = [],
        array $extra = []
    );
}
