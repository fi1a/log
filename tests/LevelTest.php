<?php

declare(strict_types=1);

namespace Fi1a\Unit\Log;

use Fi1a\Log\Level;
use Fi1a\Log\LevelInterface;
use Fi1a\Unit\Log\TestCases\LoggerTestCase;
use InvalidArgumentException;

/**
 * Уровень логирования
 */
class LevelTest extends LoggerTestCase
{
    /**
     * Уровень логирования из значения
     */
    public function testFromValue(): void
    {
        $level = Level::fromValue(LevelInterface::ALERT);
        $this->assertEquals(LevelInterface::ALERT, $level->getValue());
    }

    /**
     * Уровень логирования из значения (исключение)
     */
    public function testFromValueException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Level::fromValue(100500);
    }

    /**
     * Уровень логирования из названия
     */
    public function testFromName(): void
    {
        $level = Level::fromName('alert');
        $this->assertEquals(LevelInterface::ALERT, $level->getValue());
    }

    /**
     * Уровень логирования из названия (исключение)
     */
    public function testFromNameException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Level::fromName('unknown');
    }

    /**
     * Уровень логирования из значения или название
     */
    public function testFrom(): void
    {
        $level = Level::from(LevelInterface::ALERT);
        $this->assertEquals(LevelInterface::ALERT, $level->getValue());
        $level = Level::from('alert');
        $this->assertEquals(LevelInterface::ALERT, $level->getValue());
    }

    /**
     * Название
     */
    public function testGetName(): void
    {
        $level = Level::from(LevelInterface::ALERT);
        $this->assertEquals('ALERT', $level->getName());
    }

    /**
     * Возвращает true, если уровень меньше или равен переданному
     */
    public function testIncludesEqual(): void
    {
        $level1 = Level::from(LevelInterface::ALERT);
        $level2 = Level::from(LevelInterface::ALERT);
        $this->assertTrue($level1->includes($level2));
    }

    /**
     * Возвращает true, если уровень меньше или равен переданному
     */
    public function testIncludesLower(): void
    {
        $level1 = Level::from(LevelInterface::ALERT);
        $level2 = Level::from(LevelInterface::DEBUG);
        $this->assertFalse($level1->includes($level2));
    }

    /**
     * Возвращает true, если уровень меньше или равен переданному
     */
    public function testIncludesHigh(): void
    {
        $level1 = Level::from(LevelInterface::ALERT);
        $level2 = Level::from(LevelInterface::EMERGENCY);
        $this->assertTrue($level1->includes($level2));
    }

    /**
     * Возвращает true, если уровень выше переданного
     */
    public function testIsHigherThan(): void
    {
        $level1 = Level::from(LevelInterface::EMERGENCY);
        $level2 = Level::from(LevelInterface::ALERT);
        $this->assertTrue($level1->isHigherThan($level2));
        $this->assertFalse($level2->isHigherThan($level1));
    }

    /**
     * Возвращает true, если уровень выше переданного
     */
    public function testIsLowerThan(): void
    {
        $level1 = Level::from(LevelInterface::ALERT);
        $level2 = Level::from(LevelInterface::EMERGENCY);
        $this->assertTrue($level1->isLowerThan($level2));
        $this->assertFalse($level2->isLowerThan($level1));
    }

    /**
     * Возвращает RFC 5424 значение логирования
     */
    public function testRFC5424Value(): void
    {
        $level = Level::fromValue(LevelInterface::ALERT);
        $this->assertEquals(1, $level->getRFC5424Value());
    }
}
