<?php

declare(strict_types=1);

namespace Fi1a\Unit\Log\TestCases;

use DateTime;
use Fi1a\Log\Level;
use Fi1a\Log\LevelInterface;
use Fi1a\Log\Record;
use PHPUnit\Framework\TestCase;

/**
 * Тест-кейс логгирования
 */
class LoggerTestCase extends TestCase
{
    /**
     * Возвращает запись лога
     */
    protected function getRecord(): Record
    {
        return new Record(
            DateTime::createFromFormat('d.m.Y H:i:s', '11.01.2023 05:41:56'),
            Level::fromValue(LevelInterface::ALERT),
            'default',
            'test {{value}} message',
            [
                'value' => 'testValue',
            ],
            [
                'id' => 1,
            ]
        );
    }
}
