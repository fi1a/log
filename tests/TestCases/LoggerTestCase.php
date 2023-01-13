<?php

declare(strict_types=1);

namespace Fi1a\Unit\Log\TestCases;

use DateTime;
use Fi1a\Log\Handlers\HandlerInterface;
use Fi1a\Log\Handlers\StreamHandler;
use Fi1a\Log\Level;
use Fi1a\Log\LevelInterface;
use Fi1a\Log\Logger;
use Fi1a\Log\LoggerInterface;
use Fi1a\Log\Record;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Тест-кейс логгирования
 */
class LoggerTestCase extends TestCase
{
    protected $runtimeFolder = __DIR__ . '/../runtime';

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        if (!is_dir($this->runtimeFolder)) {
            return;
        }

        $directoryIterator = new RecursiveDirectoryIterator(
            $this->runtimeFolder,
            RecursiveDirectoryIterator::SKIP_DOTS
        );
        $filesIterator = new RecursiveIteratorIterator($directoryIterator, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($filesIterator as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($this->runtimeFolder);
    }

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

    /**
     * Возвращает обработчик
     *
     * @param resource $stream
     */
    protected function getHandler($stream, int $level): HandlerInterface
    {
        return new StreamHandler($stream, $level);
    }

    /**
     * Возвращает логгер
     */
    protected function getLogger(HandlerInterface $handler, string $channel = 'default'): LoggerInterface
    {
        $logger = new Logger($channel);
        $logger->addHandler($handler);

        return $logger;
    }
}
