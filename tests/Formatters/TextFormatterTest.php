<?php

declare(strict_types=1);

namespace Fi1a\Unit\Log\Formatters;

use DateTime;
use Fi1a\Log\Formatters\TextFormatter;
use Fi1a\Log\Level;
use Fi1a\Log\LevelInterface;
use Fi1a\Log\Record;
use Fi1a\Unit\Log\TestCases\LoggerTestCase;

use const PHP_EOL;

/**
 * Форматирование записи лога в текст
 */
class TextFormatterTest extends LoggerTestCase
{
    /**
     * Форматирование
     */
    public function testFormat()
    {
        $formatter = new TextFormatter();
        $this->assertEquals(
            '11.01.2023 05:41:56' . PHP_EOL
            . 'default.ALERT[700]' . PHP_EOL
            . 'test testValue message' . PHP_EOL
            . '{"id":1}' . PHP_EOL . PHP_EOL,
            $formatter->format($this->getRecord())
        );
    }

    /**
     * Форматирование
     */
    public function testCustomFormat()
    {
        $formatter = new TextFormatter(
            "{{datetime}}{{channel}}.{{levelName}}[{{level}}] {{message}} {{context}}\n",
            'd-m-Y H:i:s'
        );
        $this->assertEquals(
            '11-01-2023 05:41:56'
            . 'default.ALERT[700] '
            . 'test testValue message '
            . '{"id":1}' . PHP_EOL,
            $formatter->format($this->getRecord())
        );
    }

    /**
     * Форматирование с пустым контекстом
     */
    public function testFormatEmptyContext()
    {
        $record = new Record(
            DateTime::createFromFormat('d.m.Y H:i:s', '11.01.2023 05:41:56'),
            Level::fromValue(LevelInterface::ALERT),
            'default',
            'test {{value}} message',
            [
                'value' => 'testValue',
            ]
        );
        $formatter = new TextFormatter();
        $this->assertEquals(
            '11.01.2023 05:41:56' . PHP_EOL
            . 'default.ALERT[700]' . PHP_EOL
            . 'test testValue message'
            . PHP_EOL . PHP_EOL,
            $formatter->format($record)
        );
    }
}
