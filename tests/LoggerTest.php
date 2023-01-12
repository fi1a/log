<?php

declare(strict_types=1);

namespace Fi1a\Unit\Log;

use Fi1a\Log\Handlers\HandlerInterface;
use Fi1a\Log\Handlers\StreamHandler;
use Fi1a\Log\LevelInterface;
use Fi1a\Log\Logger;
use Fi1a\Log\LoggerInterface;
use Fi1a\Unit\Log\TestCases\LoggerTestCase;

/**
 * Логгер
 */
class LoggerTest extends LoggerTestCase
{
    /**
     * Возвращает обработчик
     *
     * @param resource $stream
     */
    private function getHandler($stream, int $level): HandlerInterface
    {
        return new StreamHandler($stream, $level);
    }

    private function getLogger(HandlerInterface $handler): LoggerInterface
    {
        $logger = new Logger('default');
        $logger->addHandler($handler);

        return $logger;
    }

    /**
     * Система не работает
     */
    public function testEmergency(): void
    {
        $stream = fopen('php://memory', 'rw');
        $handler = $this->getHandler(
            $stream,
            LevelInterface::EMERGENCY
        );
        $logger = $this->getLogger($handler);
        $this->assertTrue(
            $logger->emergency('log {{message}}', ['message' => 'emergency'], ['id' => 1])
        );
        $this->assertFalse(
            $logger->alert('log {{message}}', ['message' => 'alert'], ['id' => 1])
        );
        $this->assertFalse(
            $logger->critical('log {{message}}', ['message' => 'critical'], ['id' => 1])
        );
        $this->assertFalse(
            $logger->error('log {{message}}', ['message' => 'error'], ['id' => 1])
        );
        $this->assertFalse(
            $logger->warning('log {{message}}', ['message' => 'warning'], ['id' => 1])
        );
        $this->assertFalse(
            $logger->notice('log {{message}}', ['message' => 'notice'], ['id' => 1])
        );
        $this->assertFalse(
            $logger->info('log {{message}}', ['message' => 'info'], ['id' => 1])
        );
        $this->assertFalse(
            $logger->debug('log {{message}}', ['message' => 'debug'], ['id' => 1])
        );
        rewind($stream);
        $log = stream_get_contents($stream);
        $this->assertStringContainsString('log emergency', $log);
        $this->assertStringNotContainsString('log alert', $log);
        $this->assertStringNotContainsString('log critical', $log);
        $this->assertStringNotContainsString('log error', $log);
        $this->assertStringNotContainsString('log warning', $log);
        $this->assertStringNotContainsString('log notice', $log);
        $this->assertStringNotContainsString('log info', $log);
        $this->assertStringNotContainsString('log debug', $log);
        fclose($stream);
    }

    /**
     * Ошибка. Необходимо принять меры немедленно.
     */
    public function testAlert(): void
    {
        $stream = fopen('php://memory', 'rw');
        $handler = $this->getHandler(
            $stream,
            LevelInterface::ALERT
        );
        $logger = $this->getLogger($handler);
        $this->assertTrue(
            $logger->emergency('log {{message}}', ['message' => 'emergency'], ['id' => 1])
        );
        $this->assertTrue(
            $logger->alert('log {{message}}', ['message' => 'alert'], ['id' => 1])
        );
        $this->assertFalse(
            $logger->critical('log {{message}}', ['message' => 'critical'], ['id' => 1])
        );
        $this->assertFalse(
            $logger->error('log {{message}}', ['message' => 'error'], ['id' => 1])
        );
        $this->assertFalse(
            $logger->warning('log {{message}}', ['message' => 'warning'], ['id' => 1])
        );
        $this->assertFalse(
            $logger->notice('log {{message}}', ['message' => 'notice'], ['id' => 1])
        );
        $this->assertFalse(
            $logger->info('log {{message}}', ['message' => 'info'], ['id' => 1])
        );
        $this->assertFalse(
            $logger->debug('log {{message}}', ['message' => 'debug'], ['id' => 1])
        );
        rewind($stream);
        $log = stream_get_contents($stream);
        $this->assertStringContainsString('log emergency', $log);
        $this->assertStringContainsString('log alert', $log);
        $this->assertStringNotContainsString('log critical', $log);
        $this->assertStringNotContainsString('log error', $log);
        $this->assertStringNotContainsString('log warning', $log);
        $this->assertStringNotContainsString('log notice', $log);
        $this->assertStringNotContainsString('log info', $log);
        $this->assertStringNotContainsString('log debug', $log);
        fclose($stream);
    }

    /**
     * Критическая ошибка
     */
    public function testCritical(): void
    {
        $stream = fopen('php://memory', 'rw');
        $handler = $this->getHandler(
            $stream,
            LevelInterface::CRITICAL
        );
        $logger = $this->getLogger($handler);
        $this->assertTrue(
            $logger->emergency('log {{message}}', ['message' => 'emergency'], ['id' => 1])
        );
        $this->assertTrue(
            $logger->alert('log {{message}}', ['message' => 'alert'], ['id' => 1])
        );
        $this->assertTrue(
            $logger->critical('log {{message}}', ['message' => 'critical'], ['id' => 1])
        );
        $this->assertFalse(
            $logger->error('log {{message}}', ['message' => 'error'], ['id' => 1])
        );
        $this->assertFalse(
            $logger->warning('log {{message}}', ['message' => 'warning'], ['id' => 1])
        );
        $this->assertFalse(
            $logger->notice('log {{message}}', ['message' => 'notice'], ['id' => 1])
        );
        $this->assertFalse(
            $logger->info('log {{message}}', ['message' => 'info'], ['id' => 1])
        );
        $this->assertFalse(
            $logger->debug('log {{message}}', ['message' => 'debug'], ['id' => 1])
        );
        rewind($stream);
        $log = stream_get_contents($stream);
        $this->assertStringContainsString('log emergency', $log);
        $this->assertStringContainsString('log alert', $log);
        $this->assertStringContainsString('log critical', $log);
        $this->assertStringNotContainsString('log error', $log);
        $this->assertStringNotContainsString('log warning', $log);
        $this->assertStringNotContainsString('log notice', $log);
        $this->assertStringNotContainsString('log info', $log);
        $this->assertStringNotContainsString('log debug', $log);
        fclose($stream);
    }

    /**
     * Ошибка которая не требует немедленных действий, но должна быть записана
     */
    public function testError(): void
    {
        $stream = fopen('php://memory', 'rw');
        $handler = $this->getHandler(
            $stream,
            LevelInterface::ERROR
        );
        $logger = $this->getLogger($handler);
        $this->assertTrue(
            $logger->emergency('log {{message}}', ['message' => 'emergency'], ['id' => 1])
        );
        $this->assertTrue(
            $logger->alert('log {{message}}', ['message' => 'alert'], ['id' => 1])
        );
        $this->assertTrue(
            $logger->critical('log {{message}}', ['message' => 'critical'], ['id' => 1])
        );
        $this->assertTrue(
            $logger->error('log {{message}}', ['message' => 'error'], ['id' => 1])
        );
        $this->assertFalse(
            $logger->warning('log {{message}}', ['message' => 'warning'], ['id' => 1])
        );
        $this->assertFalse(
            $logger->notice('log {{message}}', ['message' => 'notice'], ['id' => 1])
        );
        $this->assertFalse(
            $logger->info('log {{message}}', ['message' => 'info'], ['id' => 1])
        );
        $this->assertFalse(
            $logger->debug('log {{message}}', ['message' => 'debug'], ['id' => 1])
        );
        rewind($stream);
        $log = stream_get_contents($stream);
        $this->assertStringContainsString('log emergency', $log);
        $this->assertStringContainsString('log alert', $log);
        $this->assertStringContainsString('log critical', $log);
        $this->assertStringContainsString('log error', $log);
        $this->assertStringNotContainsString('log warning', $log);
        $this->assertStringNotContainsString('log notice', $log);
        $this->assertStringNotContainsString('log info', $log);
        $this->assertStringNotContainsString('log debug', $log);
        fclose($stream);
    }

    /**
     * Предупреждение
     */
    public function testWarning(): void
    {
        $stream = fopen('php://memory', 'rw');
        $handler = $this->getHandler(
            $stream,
            LevelInterface::WARNING
        );
        $logger = $this->getLogger($handler);
        $this->assertTrue(
            $logger->emergency('log {{message}}', ['message' => 'emergency'], ['id' => 1])
        );
        $this->assertTrue(
            $logger->alert('log {{message}}', ['message' => 'alert'], ['id' => 1])
        );
        $this->assertTrue(
            $logger->critical('log {{message}}', ['message' => 'critical'], ['id' => 1])
        );
        $this->assertTrue(
            $logger->error('log {{message}}', ['message' => 'error'], ['id' => 1])
        );
        $this->assertTrue(
            $logger->warning('log {{message}}', ['message' => 'warning'], ['id' => 1])
        );
        $this->assertFalse(
            $logger->notice('log {{message}}', ['message' => 'notice'], ['id' => 1])
        );
        $this->assertFalse(
            $logger->info('log {{message}}', ['message' => 'info'], ['id' => 1])
        );
        $this->assertFalse(
            $logger->debug('log {{message}}', ['message' => 'debug'], ['id' => 1])
        );
        rewind($stream);
        $log = stream_get_contents($stream);
        $this->assertStringContainsString('log emergency', $log);
        $this->assertStringContainsString('log alert', $log);
        $this->assertStringContainsString('log critical', $log);
        $this->assertStringContainsString('log error', $log);
        $this->assertStringContainsString('log warning', $log);
        $this->assertStringNotContainsString('log notice', $log);
        $this->assertStringNotContainsString('log info', $log);
        $this->assertStringNotContainsString('log debug', $log);
        fclose($stream);
    }

    /**
     * Все нормально, но событие значимое
     */
    public function testNotice(): void
    {
        $stream = fopen('php://memory', 'rw');
        $handler = $this->getHandler(
            $stream,
            LevelInterface::NOTICE
        );
        $logger = $this->getLogger($handler);
        $this->assertTrue(
            $logger->emergency('log {{message}}', ['message' => 'emergency'], ['id' => 1])
        );
        $this->assertTrue(
            $logger->alert('log {{message}}', ['message' => 'alert'], ['id' => 1])
        );
        $this->assertTrue(
            $logger->critical('log {{message}}', ['message' => 'critical'], ['id' => 1])
        );
        $this->assertTrue(
            $logger->error('log {{message}}', ['message' => 'error'], ['id' => 1])
        );
        $this->assertTrue(
            $logger->warning('log {{message}}', ['message' => 'warning'], ['id' => 1])
        );
        $this->assertTrue(
            $logger->notice('log {{message}}', ['message' => 'notice'], ['id' => 1])
        );
        $this->assertFalse(
            $logger->info('log {{message}}', ['message' => 'info'], ['id' => 1])
        );
        $this->assertFalse(
            $logger->debug('log {{message}}', ['message' => 'debug'], ['id' => 1])
        );
        rewind($stream);
        $log = stream_get_contents($stream);
        $this->assertStringContainsString('log emergency', $log);
        $this->assertStringContainsString('log alert', $log);
        $this->assertStringContainsString('log critical', $log);
        $this->assertStringContainsString('log error', $log);
        $this->assertStringContainsString('log warning', $log);
        $this->assertStringContainsString('log notice', $log);
        $this->assertStringNotContainsString('log info', $log);
        $this->assertStringNotContainsString('log debug', $log);
        fclose($stream);
    }

    /**
     * Какое либо событие
     */
    public function testInfo(): void
    {
        $stream = fopen('php://memory', 'rw');
        $handler = $this->getHandler(
            $stream,
            LevelInterface::INFO
        );
        $logger = $this->getLogger($handler);
        $this->assertTrue(
            $logger->emergency('log {{message}}', ['message' => 'emergency'], ['id' => 1])
        );
        $this->assertTrue(
            $logger->alert('log {{message}}', ['message' => 'alert'], ['id' => 1])
        );
        $this->assertTrue(
            $logger->critical('log {{message}}', ['message' => 'critical'], ['id' => 1])
        );
        $this->assertTrue(
            $logger->error('log {{message}}', ['message' => 'error'], ['id' => 1])
        );
        $this->assertTrue(
            $logger->warning('log {{message}}', ['message' => 'warning'], ['id' => 1])
        );
        $this->assertTrue(
            $logger->notice('log {{message}}', ['message' => 'notice'], ['id' => 1])
        );
        $this->assertTrue(
            $logger->info('log {{message}}', ['message' => 'info'], ['id' => 1])
        );
        $this->assertFalse(
            $logger->debug('log {{message}}', ['message' => 'debug'], ['id' => 1])
        );
        rewind($stream);
        $log = stream_get_contents($stream);
        $this->assertStringContainsString('log emergency', $log);
        $this->assertStringContainsString('log alert', $log);
        $this->assertStringContainsString('log critical', $log);
        $this->assertStringContainsString('log error', $log);
        $this->assertStringContainsString('log warning', $log);
        $this->assertStringContainsString('log notice', $log);
        $this->assertStringContainsString('log info', $log);
        $this->assertStringNotContainsString('log debug', $log);
        fclose($stream);
    }

    /**
     * Отладочная информация
     */
    public function testDebug(): void
    {
        $stream = fopen('php://memory', 'rw');
        $handler = $this->getHandler(
            $stream,
            LevelInterface::DEBUG
        );
        $logger = $this->getLogger($handler);
        $this->assertTrue(
            $logger->emergency('log {{message}}', ['message' => 'emergency'], ['id' => 1])
        );
        $this->assertTrue(
            $logger->alert('log {{message}}', ['message' => 'alert'], ['id' => 1])
        );
        $this->assertTrue(
            $logger->critical('log {{message}}', ['message' => 'critical'], ['id' => 1])
        );
        $this->assertTrue(
            $logger->error('log {{message}}', ['message' => 'error'], ['id' => 1])
        );
        $this->assertTrue(
            $logger->warning('log {{message}}', ['message' => 'warning'], ['id' => 1])
        );
        $this->assertTrue(
            $logger->notice('log {{message}}', ['message' => 'notice'], ['id' => 1])
        );
        $this->assertTrue(
            $logger->info('log {{message}}', ['message' => 'info'], ['id' => 1])
        );
        $this->assertTrue(
            $logger->debug('log {{message}}', ['message' => 'debug'], ['id' => 1])
        );
        rewind($stream);
        $log = stream_get_contents($stream);
        $this->assertStringContainsString('log emergency', $log);
        $this->assertStringContainsString('log alert', $log);
        $this->assertStringContainsString('log critical', $log);
        $this->assertStringContainsString('log error', $log);
        $this->assertStringContainsString('log warning', $log);
        $this->assertStringContainsString('log notice', $log);
        $this->assertStringContainsString('log info', $log);
        $this->assertStringContainsString('log debug', $log);
        fclose($stream);
    }

    /**
     * Контекстная информация
     */
    public function testWithContext(): void
    {
        $stream = fopen('php://memory', 'rw');
        $handler = $this->getHandler(
            $stream,
            LevelInterface::EMERGENCY
        );
        $logger = $this->getLogger($handler);
        $logger->withContext(['key1' => 'log emergency']);
        $logger->withContext(['key2' => 'log alert']);
        $this->assertTrue(
            $logger->emergency('message', [], ['key3' => 'log critical'])
        );
        rewind($stream);
        $log = stream_get_contents($stream);
        $this->assertStringContainsString('"key1":"log emergency"', $log);
        $this->assertStringContainsString('"key2":"log alert"', $log);
        $this->assertStringContainsString('"key3":"log critical"', $log);
        fclose($stream);
    }

    public function testHandlers(): void
    {
        $logger = new Logger('default');

        $this->assertCount(0, $logger->getHandlers());

        $stream = fopen('php://memory', 'rw');
        $handler1 = $this->getHandler(
            $stream,
            LevelInterface::EMERGENCY
        );
        $handler2 = $this->getHandler(
            $stream,
            LevelInterface::ALERT
        );
        $logger->setHandlers([$handler1, $handler2]);

        $this->assertCount(2, $logger->getHandlers());

        $this->assertTrue($logger->emergency('message'));
        $this->assertTrue($logger->alert('message'));
        $this->assertFalse($logger->critical('message'));
    }
}
