<?php

declare(strict_types=1);

namespace Fi1a\Unit\Log;

use Fi1a\Log\ChannelsInterface;
use Fi1a\Log\LevelInterface;
use Fi1a\Log\LoggerInterface;
use Fi1a\Unit\Log\TestCases\LoggerTestCase;

/**
 * Dependency injection
 */
class DiTest extends LoggerTestCase
{
    /**
     * Каналы
     */
    public function testChannels(): void
    {
        $this->assertInstanceOf(ChannelsInterface::class, di()->get(ChannelsInterface::class));
    }

    /**
     * Каналы
     */
    public function testLogger(): void
    {
        $removeDefault = false;
        if (!logger()->hasChannel('default')) {
            $stream = fopen('php://memory', 'rw');
            $handler = $this->getHandler(
                $stream,
                LevelInterface::EMERGENCY
            );
            $logger = $this->getLogger($handler);
            logger()->addChannel($logger);
            $removeDefault = true;
        }

        $this->assertInstanceOf(LoggerInterface::class, di()->get(LoggerInterface::class));
        if ($removeDefault) {
            logger()->deleteChannel('default');
        }
    }
}
