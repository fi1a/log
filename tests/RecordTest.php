<?php

declare(strict_types=1);

namespace Fi1a\Unit\Log;

use DateTime;
use Fi1a\Log\Level;
use Fi1a\Log\LevelInterface;
use Fi1a\Log\Record;
use Fi1a\Unit\Log\TestCases\LoggerTestCase;

/**
 * Запись лога
 */
class RecordTest extends LoggerTestCase
{
    /**
     * Запись лога
     */
    public function testRecord(): void
    {
        $datetime = new DateTime();
        $record = new Record(
            $datetime,
            Level::fromValue(LevelInterface::ALERT),
            'default',
            'test {{value}} message',
            [
                'value' => 'test',
            ],
            [
                'id' => 1,
            ]
        );
        $this->assertEquals($datetime, $record->datetime);
        $this->assertEquals(LevelInterface::ALERT, $record->level->getValue());
        $this->assertEquals('default', $record->channel);
        $this->assertEquals('test {{value}} message', $record->message);
        $this->assertEquals(['value' => 'test',], $record->values);
        $this->assertEquals(['id' => 1,], $record->context);
    }
}
