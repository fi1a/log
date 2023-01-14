<?php

declare(strict_types=1);

namespace Fi1a\Log\Formatters;

use Fi1a\Format\Formatter;
use Fi1a\Log\Record;

/**
 * Форматирование записи лога в текст
 */
class TextFormatter extends AbstractFormatter
{
    public const DEFAULT_FORMAT = "{{datetime}}\n{{channel}}.{{levelName}}[{{level}}]\n{{message}}\n{{context}}\n\n";

    /**
     * @var string
     */
    private $format;

    public function __construct(?string $format = null, ?string $dateFormat = null)
    {
        if ($format === null) {
            /** @var string $format */
            $format = static::DEFAULT_FORMAT;
        }
        $this->format = $format;
        parent::__construct($dateFormat);
    }

    /**
     * @inheritDoc
     */
    public function format(Record $record)
    {
        return Formatter::format(
            $this->format,
            [
                'datetime' =>  $this->getDateFormat($record),
                'channel' => $record->channel,
                'levelName' => $record->level->getName(),
                'level' => $record->level->getValue(),
                'message' => Formatter::format(
                    $record->message,
                    $record->values,
                    [],
                    false
                ),
                'context' => count($record->context) ? json_encode($record->context) : '',
            ],
            [],
            false
        );
    }
}
