<?php

declare(strict_types=1);

namespace Fi1a\Unit\Log;

use Fi1a\Log\Channels;
use Fi1a\Log\LevelInterface;
use Fi1a\Log\LoggerInterface;
use Fi1a\Unit\Log\TestCases\LoggerTestCase;

/**
 * Каналы логирования
 */
class ChannelsTest extends LoggerTestCase
{
    /**
     * Каналы логирования
     */
    public function testChannel()
    {
        $channels = new Channels();

        $stream = fopen('php://memory', 'rw');
        $handler = $this->getHandler(
            $stream,
            LevelInterface::EMERGENCY
        );

        $this->assertFalse($channels->hasChannel('channel1'));
        $logger = $this->getLogger($handler, 'channel1');
        $channels->addChannel($logger);
        $this->assertTrue($channels->hasChannel('channel1'));

        $this->assertFalse($channels->hasChannel('channel2'));
        $logger = $this->getLogger($handler, 'channel2');
        $channels->addChannel($logger);
        $this->assertTrue($channels->hasChannel('channel2'));

        $this->assertInstanceOf(LoggerInterface::class, $channels->channel('channel1'));
        $this->assertInstanceOf(LoggerInterface::class, $channels->channel('channel2'));
        $this->assertFalse($channels->channel('unknown'));
        $this->assertFalse($channels->channel());

        $logger = $this->getLogger($handler);
        $channels->addChannel($logger);
        $this->assertInstanceOf(LoggerInterface::class, $channels->channel());

        $this->assertTrue($channels->deleteChannel('channel1'));
        $this->assertFalse($channels->deleteChannel('channel1'));
        $this->assertFalse($channels->hasChannel('channel1'));
        $this->assertTrue($channels->hasChannel('channel2'));
    }

    /**
     * Контекстная информация
     */
    public function testWithContext()
    {
        $channels = new Channels();

        $stream = fopen('php://memory', 'rw');
        $handler = $this->getHandler(
            $stream,
            LevelInterface::EMERGENCY
        );
        $logger = $this->getLogger($handler, 'channel1');
        $channels->addChannel($logger);

        $channels->withContext(['key1' => 'log emergency']);
        $channels->withContext(['key2' => 'log alert']);
        $this->assertTrue(
            $channels->channel('channel1')
                ->emergency('message', [], ['key3' => 'log critical'])
        );
        rewind($stream);
        $log = stream_get_contents($stream);
        $this->assertStringContainsString('"key1":"log emergency"', $log);
        $this->assertStringContainsString('"key2":"log alert"', $log);
        $this->assertStringContainsString('"key3":"log critical"', $log);
        fclose($stream);
    }
}
