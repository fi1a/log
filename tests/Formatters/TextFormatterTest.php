<?php

declare(strict_types=1);

namespace Fi1a\Unit\Log\Formatters;

use Fi1a\Log\Formatters\TextFormatter;
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
            . '{&quot;id&quot;:1}' . PHP_EOL . PHP_EOL,
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
            . '{&quot;id&quot;:1}' . PHP_EOL,
            $formatter->format($this->getRecord())
        );
    }
}
