<?php

declare(strict_types=1);

namespace Fi1a\Unit\Log\Handlers;

use ErrorException;
use Fi1a\Log\Formatters\TextFormatter;
use Fi1a\Log\Handlers\StreamHandler;
use Fi1a\Log\Level;
use Fi1a\Log\LevelInterface;
use Fi1a\Unit\Log\TestCases\LoggerTestCase;
use InvalidArgumentException;

/**
 * Обработчик логирования в файле (поток)
 */
class StreamHandlerTest extends LoggerTestCase
{
    /**
     * По уровню логирования определяет будет ли выполнено оно
     */
    public function testIsHandlingTrue(): void
    {
        $stream = fopen('php://memory', 'w');
        $handler = new StreamHandler($stream, LevelInterface::ALERT);
        $this->assertEquals(LevelInterface::ALERT, $handler->getLevel()->getValue());
        $this->assertTrue($handler->isHandling(Level::fromValue(LevelInterface::ALERT)));
        $this->assertFalse($handler->isHandling(Level::fromValue(LevelInterface::DEBUG)));
    }

    /**
     * Метод обработчик
     */
    public function testHandle(): void
    {
        $formatter = new TextFormatter();
        $stream = fopen('php://memory', 'rw');
        $record = $this->getRecord();
        $handler = new StreamHandler($stream, LevelInterface::ALERT);
        $this->assertTrue($handler->handle($record));
        $handler->setLevel(LevelInterface::EMERGENCY);
        $this->assertFalse($handler->handle($record));
        rewind($stream);
        $this->assertEquals(
            $formatter->format($record),
            stream_get_contents($stream)
        );
        fclose($stream);
    }

    /**
     * Метод обработчик
     */
    public function testHandleException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new StreamHandler('', LevelInterface::ALERT);
    }

    /**
     * Метод обработчик
     */
    public function testHandleFile(): void
    {
        $filePath = $this->runtimeFolder . '/log.log';

        $formatter = new TextFormatter();
        $record = $this->getRecord();
        $handler = new StreamHandler($filePath, LevelInterface::ALERT, 0777, true);
        $this->assertTrue($handler->handle($record));
        $handler->setLevel(LevelInterface::EMERGENCY);
        $this->assertFalse($handler->handle($record));
        $handler->close();
        $stream = fopen($filePath, 'r');
        rewind($stream);
        $this->assertEquals(
            $formatter->format($record),
            stream_get_contents($stream)
        );
        fclose($stream);
    }

    /**
     * Метод обработчик
     */
    public function testHandleFileCreateDirException(): void
    {
        $this->expectException(ErrorException::class);
        $filePath = $this->runtimeFolder . '/log.log';

        $handler = $this->getMockBuilder(StreamHandler::class)
            ->setConstructorArgs([$filePath, LevelInterface::ALERT])
            ->onlyMethods(['createDir'])
            ->getMock();

        $handler->method('createDir')->willReturn(false);
        $record = $this->getRecord();
        $handler->handle($record);
    }

    /**
     * Метод обработчик
     */
    public function testHandleOpenFileException(): void
    {
        $this->expectException(ErrorException::class);
        $filePath = $this->runtimeFolder . '/log.log';

        $handler = $this->getMockBuilder(StreamHandler::class)
            ->setConstructorArgs([$filePath, LevelInterface::ALERT])
            ->onlyMethods(['isResource'])
            ->getMock();

        $handler->method('isResource')->willReturn(false);
        $record = $this->getRecord();
        $handler->handle($record);
    }
}
