<?php

declare(strict_types=1);

namespace Fi1a\Unit\Log\Handlers;

use Fi1a\Log\Handlers\SyslogHandler;
use Fi1a\Unit\Log\TestCases\LoggerTestCase;
use LogicException;
use UnexpectedValueException;

/**
 * Логирование в syslog
 */
class SyslogHandlerTest extends LoggerTestCase
{
    /**
     * Логирование в syslog
     */
    public function testSyslog(): void
    {
        $handler = $this->getMockBuilder(SyslogHandler::class)
            ->setConstructorArgs([])
            ->onlyMethods(['syslog'])
            ->getMock();

        $handler->method('syslog')->willReturn(true);

        $record = $this->getRecord();
        $this->assertTrue($handler->handle($record));
        $handler->close();
    }

    /**
     * Логирование в syslog
     */
    public function testSyslogFacility(): void
    {
        $handler = $this->getMockBuilder(SyslogHandler::class)
            ->setConstructorArgs(['', 'auth'])
            ->onlyMethods(['syslog'])
            ->getMock();

        $handler->method('syslog')->willReturn(true);

        $record = $this->getRecord();
        $this->assertTrue($handler->handle($record));
        $handler->close();
    }

    /**
     * Исключенеи при ошибке открытия syslog
     */
    public function testSyslogOpenError(): void
    {
        $this->expectException(LogicException::class);

        $handler = $this->getMockBuilder(SyslogHandler::class)
            ->setConstructorArgs([])
            ->onlyMethods(['openSyslog'])
            ->getMock();

        $handler->method('openSyslog')->willReturn(false);

        $record = $this->getRecord();
        $handler->handle($record);
    }

    /**
     * Исключение при неизвестном facility
     */
    public function testFacilityError(): void
    {
        $this->expectException(UnexpectedValueException::class);
        new SyslogHandler('', 100500);
    }
}
