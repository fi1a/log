<?php

declare(strict_types=1);

namespace Fi1a\Log\Formatters;

use Fi1a\Format\Formatter;
use Fi1a\Log\Record;

/**
 * Форматирование записи лога в текст
 */
class TextFormatter implements FormatterInterface
{
    public const DEFAULT_FORMAT = "{{datetime}}\n{{channel}}.{{levelName}}[{{level}}]\n{{message}}\n{{context}}\n\n";

    /**
     * @var string
     */
    private $format;

    /**
     * @var string
     */
    private $dateFormat;

    public function __construct(?string $format = null, ?string $dateFormat = null)
    {
        if ($format === null) {
            /** @var string $format */
            $format = static::DEFAULT_FORMAT;
        }
        $this->format = $format;
        if ($dateFormat === null) {
            $dateFormat = 'd.m.Y H:i:s';
        }
        $this->dateFormat = $dateFormat;
    }

    /**
     * @inheritDoc
     */
    public function format(Record $record)
    {
        return Formatter::format(
            $this->format,
            [
                'datetime' =>  $record->datetime->format($this->dateFormat),
                'channel' => $record->channel,
                'levelName' => $record->level->getName(),
                'level' => $record->level->getValue(),
                'message' => Formatter::format(
                    $record->message,
                    $record->values,
                    []
                ),
                'context' => count($record->context) ? json_encode($record->context) : '',
            ],
            []
        );
    }
}