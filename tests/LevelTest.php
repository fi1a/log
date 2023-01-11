<?php

declare(strict_types=1);

namespace Fi1a\Unit\Log;

use Fi1a\Log\Level;
use Fi1a\Log\LevelInterface;
use PHPUnit\Framework\TestCase;

/**
 * Уровень логирования
 */
class LevelTest extends TestCase
{
    /**
     * Уровень логирования
     */
    public function testLevel(): void
    {
        $level = new Level(LevelInterface::ALERT);
        $this->assertEquals(LevelInterface::ALERT, $level->getLevel());
    }
}
