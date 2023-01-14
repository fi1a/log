<?php

declare(strict_types=1);

namespace Fi1a\Log\Formatters;

use Fi1a\Format\Formatter;
use Fi1a\Log\LevelInterface;
use Fi1a\Log\Record;

use const ENT_NOQUOTES;
use const JSON_INVALID_UTF8_SUBSTITUTE;
use const JSON_PARTIAL_OUTPUT_ON_ERROR;
use const JSON_PRESERVE_ZERO_FRACTION;
use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

/**
 * Форматирование записи лога в виде html
 */
class HtmlFormatter extends AbstractFormatter
{
    /**
     * @var array<int, string>
     */
    protected static $levelColors = [
        LevelInterface::DEBUG => '#b2b2b2',
        LevelInterface::INFO => '#24943d',
        LevelInterface::NOTICE => '#138a9d',
        LevelInterface::WARNING => '#eab106',
        LevelInterface::ERROR => '#e76f0a',
        LevelInterface::CRITICAL => '#c42f3d',
        LevelInterface::ALERT => '#75151e',
        LevelInterface::EMERGENCY => '#000000',
    ];

    /**
     * Цвет уровня записи лога
     */
    protected function getLevelColor(LevelInterface $level): string
    {
        return static::$levelColors[$level->getValue()];
    }

    /**
     * Добавить заголовок
     */
    protected function addTitle(string $title, LevelInterface $level): string
    {
        $title = htmlspecialchars($title, ENT_NOQUOTES);

        return '<h1 style="background: ' . $this->getLevelColor($level)
            . '; color: #ffffff; padding: 10px; margin: 10px 0;">' . $title . '[' . $level->getValue() . ']' . '</h1>';
    }

    /**
     * Добавить строку таблицы
     */
    protected function addRow(string $title, string $body, LevelInterface $level, bool $escapeBody = true): string
    {
        $title = htmlspecialchars($title, ENT_NOQUOTES);
        if ($escapeBody) {
            $body = '<pre>' . htmlspecialchars($body, ENT_NOQUOTES) . '</pre>';
        }

        return '<tr style="padding: 4px; text-align: left;">'
            . '<th style="vertical-align: top; background: ' . $this->getLevelColor($level)
            . '; color: #fff; padding: 10px;" width="100">' . $title
            . '</th><td style="padding: 10px; text-align: left; vertical-align: top; background: #eee; color: #000">'
            . $body . '</td></tr>';
    }

    /**
     * @inheritDoc
     */
    public function format(Record $record)
    {
        $output = '<div style="padding: 0 10px;">';
        $output .= $this->addTitle($record->level->getName(), $record->level);
        $output .= '<table cellspacing="1" width="100%">';

        $output .= $this->addRow('Время', $this->getDateFormat($record), $record->level);
        $output .= $this->addRow('Канал', $record->channel, $record->level);
        $output .= $this->addRow(
            'Сообщение',
            Formatter::format(
                $record->message,
                $record->values,
                [],
                false
            ),
            $record->level
        );
        if (count($record->context)) {
            $output .= $this->addRow(
                'Контекст',
                json_encode($record->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                    | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION
                    | JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR),
                $record->level,
                false
            );
        }
        $output .= '</table>';
        $output .= '</div>';

        return $output;
    }
}
