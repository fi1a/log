<?php

declare(strict_types=1);

namespace Fi1a\Log\Formatters;

use Fi1a\Log\RecordInterface;

/**
 * Форматирование записи лога
 */
interface FormatterInterface
{
    /**
     * Форматирует запись лога
     *
     * @return mixed
     */
    public function format(RecordInterface $record);
}
