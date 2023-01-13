<?php

declare(strict_types=1);

namespace Fi1a\Unit\Log\Handlers;

use Closure;
use DateTime;
use Fi1a\Log\Handlers\RotatingFileHandler;
use Fi1a\Log\Handlers\StreamHandler;
use Fi1a\Log\Level;
use Fi1a\Log\LevelInterface;
use Fi1a\Log\Record;
use Fi1a\Unit\Log\TestCases\LoggerTestCase;
use FilesystemIterator;
use InvalidArgumentException;

/**
 * Сохраняет логи в файлах с именем по маске времени. Хранится ограниченное кол-во файлов
 */
class RotatingFileHandlerTest extends LoggerTestCase
{
    /**
     * Сохраняет логи в файлах с именем по маске времени. Хранится ограниченное кол-во файлов
     */
    public function testRotate(): void
    {
        $filePath = $this->runtimeFolder . '/log.log';
        $record = $this->getRecord();

        $handler = new RotatingFileHandler($filePath, 1, null, LevelInterface::ALERT);
        $this->assertTrue($handler->handle($record));

        $record = new Record(
            new DateTime('tomorrow'),
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
        $this->assertTrue($handler->handle($record));
    }

    /**
     * Закрытие открытых логов по наступлению следующего дня
     */
    public function testRotateWithCloseStream(): void
    {
        $handler = $this->getMockWithRotate();

        $record = new Record(
            new DateTime(),
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
        $this->assertTrue($handler->handle($record));

        $record = new Record(
            new DateTime('tomorrow'),
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
        $this->assertTrue($handler->handle($record));

        $record = new Record(
            (new DateTime())->modify('+2 days'),
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
        $this->assertTrue($handler->handle($record));

        $record = new Record(
            (new DateTime())->modify('+3 days'),
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
        $this->assertTrue($handler->handle($record));

        $fi = new FilesystemIterator($this->runtimeFolder, FilesystemIterator::SKIP_DOTS);
        $this->assertEquals(1, iterator_count($fi));
    }

    /**
     * Ротация файлов отключена
     */
    public function testRotateMaxFiles(): void
    {
        $filePath = $this->runtimeFolder . '/log.log';
        $record = $this->getRecord();

        $handler = new RotatingFileHandler($filePath, 0, null, LevelInterface::ALERT);
        $this->assertTrue($handler->handle($record));

        $record = new Record(
            new DateTime(),
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
        $this->assertTrue($handler->handle($record));
    }

    /**
     * Исключение при пустом пути к файлу
     */
    public function testFileNameException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new RotatingFileHandler('', 1, 'd.m.Y', LevelInterface::ALERT);
    }

    /**
     * Glob вернул ошибку
     */
    public function testGlobErrorOnRotate(): void
    {
        $handler = $this->getMockWithRotate(['glob']);

        $handler->method('glob')->willReturn(false);

        $record = new Record(
            new DateTime(),
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
        $this->assertTrue($handler->handle($record));

        $record = new Record(
            new DateTime('tomorrow'),
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
        $this->assertTrue($handler->handle($record));

        $record = new Record(
            (new DateTime())->modify('+2 days'),
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
        $this->assertTrue($handler->handle($record));

        $fi = new FilesystemIterator($this->runtimeFolder, FilesystemIterator::SKIP_DOTS);
        $this->assertEquals(3, iterator_count($fi));
    }

    /**
     * Glob вернул ошибку
     */
    public function testIsWritableFalseOnRotate(): void
    {
        $handler = $this->getMockWithRotate(['isWritable']);

        $handler->method('isWritable')->willReturn(false);

        $record = new Record(
            new DateTime(),
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
        $this->assertTrue($handler->handle($record));

        $record = new Record(
            new DateTime('tomorrow'),
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
        $this->assertTrue($handler->handle($record));

        $record = new Record(
            (new DateTime())->modify('+2 days'),
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
        $this->assertTrue($handler->handle($record));

        $fi = new FilesystemIterator($this->runtimeFolder, FilesystemIterator::SKIP_DOTS);
        $this->assertEquals(3, iterator_count($fi));
    }

    /**
     * Возвращает обработчик с ротацией на каждую запись
     *
     * @param string[] $onlyMethods
     *
     * @return (RotatingFileHandler&\PHPUnit\Framework\MockObject\MockObject)|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getMockWithRotate(array $onlyMethods = [])
    {
        $filePath = $this->runtimeFolder . '/log.log';
        $runtimeFolder = $this->runtimeFolder;

        $handler = $this->getMockBuilder(RotatingFileHandler::class)
            ->onlyMethods(array_merge($onlyMethods, ['getFileName']))
            ->disableOriginalConstructor()
            ->getMock();

        $set = Closure::bind(
            function (string $filePathAndName) {
                $this->filePathAndName = $filePathAndName;
                $this->maxFiles = 1;
                $this->fileNameFormat = '{{filename}}-{{date}}';
                $this->dateFormat = 'Y-m-d';
                $this->next = new DateTime('tomorrow');
                StreamHandler::__construct($this->getFileName(), LevelInterface::ALERT);
            },
            $handler,
            get_class($handler)
        );

        $handler->method('getFileName')->willReturnCallback(function () use ($runtimeFolder) {
            static $dateTime = null;
            if ($dateTime === null) {
                $dateTime = new DateTime();
            } else {
                $dateTime->modify('+1 day');
            }

            return $runtimeFolder . '/log-' . $dateTime->format('Y-m-d') . '.log';
        });

        $set($filePath);

        return $handler;
    }
}
