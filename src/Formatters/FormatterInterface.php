<?php

declare(strict_types=1);

namespace Fi1a\Log\Formatters;

use Fi1a\Log\Record;

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
    public function format(Record $record);
}
