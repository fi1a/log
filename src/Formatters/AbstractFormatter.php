<?php

declare(strict_types=1);

namespace Fi1a\Log\Formatters;

use Fi1a\Log\Record;

/**
 * Форматирование записи лога
 */
abstract class AbstractFormatter implements FormatterInterface
{
    /**
     * @var string
     */
    protected $dateFormat;

    public function __construct(?string $dateFormat = null)
    {
        if ($dateFormat === null) {
            $dateFormat = 'd.m.Y H:i:s';
        }
        $this->dateFormat = $dateFormat;
    }

    /**
     * Форматирование даты
     */
    protected function getDateFormat(Record $record): string
    {
        return $record->datetime->format($this->dateFormat);
    }
}
