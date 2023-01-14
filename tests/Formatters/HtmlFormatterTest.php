<?php

declare(strict_types=1);

namespace Fi1a\Unit\Log\Formatters;

use Fi1a\Log\Formatters\HtmlFormatter;
use Fi1a\Unit\Log\TestCases\LoggerTestCase;

/**
 * Форматирование записи лога в виде html
 */
class HtmlFormatterTest extends LoggerTestCase
{
    /**
     * Форматирование
     */
    public function testFormat()
    {
        $formatter = new HtmlFormatter();
        $this->assertIsString($formatter->format($this->getRecord()));
    }
}
