<?php

declare(strict_types=1);

namespace Fi1a\Unit\Log\Handlers;

use Fi1a\Log\Handlers\MailHandler;
use Fi1a\Unit\Log\TestCases\LoggerTestCase;
use InvalidArgumentException;

/**
 * Отправка логов на почту
 */
class MailHandlerTest extends LoggerTestCase
{
    /**
     * Отправка логов на почту
     */
    public function testMailHandler(): void
    {
        $handler = $this->getMockBuilder(MailHandler::class)
            ->setConstructorArgs(['fake@fi1a.ru', 'test@fi1a.ru'])
            ->onlyMethods(['mail'])
            ->getMock();

        $handler->expects($this->exactly(2))->method('mail')->willReturn(true);

        $this->assertTrue($handler->handle($this->getRecord()));
        $this->assertTrue($handler->handle($this->getRecord()));
    }

    /**
     * Отправка логов на почту
     */
    public function testMailBatchHandler(): void
    {
        $handler = $this->getMockBuilder(MailHandler::class)
            ->setConstructorArgs(['fake@fi1a.ru', 'test@fi1a.ru', 2])
            ->onlyMethods(['mail'])
            ->getMock();

        $handler->expects($this->exactly(2))->method('mail')->willReturn(true);

        $this->assertTrue($handler->handle($this->getRecord()));
        $this->assertTrue($handler->handle($this->getRecord()));

        $this->assertTrue($handler->handle($this->getRecord()));
        $handler->close();
    }

    /**
     * Тип контента письма
     */
    public function testContentType(): void
    {
        $handler = new MailHandler('fake@fi1a.ru', 'test@fi1a.ru');
        $this->assertEquals('text/html', $handler->getContentType());
        $handler->setContentType('text/plain');
        $this->assertEquals('text/plain', $handler->getContentType());
    }

    /**
     * Тип контента письма
     */
    public function testContentTypeException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $handler = new MailHandler('fake@fi1a.ru', 'test@fi1a.ru');
        $handler->setContentType('');
    }

    /**
     * Кодировка письма
     */
    public function testEncoding(): void
    {
        $handler = new MailHandler('fake@fi1a.ru', 'test@fi1a.ru');
        $this->assertEquals('utf-8', $handler->getEncoding());
        $handler->setEncoding('windows-1251');
        $this->assertEquals('windows-1251', $handler->getEncoding());
    }

    /**
     * Кодировка письма
     */
    public function testEncodingException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $handler = new MailHandler('fake@fi1a.ru', 'test@fi1a.ru');
        $handler->setEncoding('');
    }

    /**
     * Заголовки
     */
    public function testHeaders(): void
    {
        $handler = $this->getMockBuilder(MailHandler::class)
            ->setConstructorArgs(['fake@fi1a.ru', 'test@fi1a.ru'])
            ->onlyMethods(['mail'])
            ->getMock();

        $handler->expects($this->once())->method('mail')->willReturn(true);

        $handler->addHeaders([
            'X-Mailer: PHP/' . phpversion(),
            'X-Priority: 1',
        ]);

        $this->assertTrue($handler->handle($this->getRecord()));
    }

    /**
     * Заголовки
     */
    public function testHeaderException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $handler = new MailHandler('fake@fi1a.ru', 'test@fi1a.ru');
        $handler->addHeader("X-Priority: 1\n");
    }

    /**
     * Заголовки
     */
    public function testParameters(): void
    {
        $handler = $this->getMockBuilder(MailHandler::class)
            ->setConstructorArgs(['fake@fi1a.ru', 'test@fi1a.ru'])
            ->onlyMethods(['mail'])
            ->getMock();

        $handler->expects($this->once())->method('mail')->willReturn(true);

        $handler->addParameters([
            '-fake@fi1a.ru',
        ]);

        $this->assertTrue($handler->handle($this->getRecord()));
    }
}
