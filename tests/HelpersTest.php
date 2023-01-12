<?php

declare(strict_types=1);

namespace Fi1a\Unit\Log;

use Fi1a\Log\ChannelsInterface;
use Fi1a\Log\LevelInterface;
use Fi1a\Log\LoggerInterface;
use Fi1a\Unit\Log\TestCases\LoggerTestCase;

/**
 * Хелперы
 */
class HelpersTest extends LoggerTestCase
{
    /**
     * Хелпер логирования
     */
    public function testLogger(): void
    {
        $stream = fopen('php://memory', 'rw');
        $handler = $this->getHandler(
            $stream,
            LevelInterface::EMERGENCY
        );

        $this->assertInstanceOf(ChannelsInterface::class, logger());
        $logger = $this->getLogger($handler, 'channel1');
        logger()->addChannel($logger);
        $this->assertInstanceOf(LoggerInterface::class, logger('channel1'));
        $this->assertFalse(logger('unknown'));
    }
}
