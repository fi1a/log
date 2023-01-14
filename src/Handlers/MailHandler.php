<?php

declare(strict_types=1);

namespace Fi1a\Log\Handlers;

use Fi1a\Log\Formatters\FormatterInterface;
use Fi1a\Log\Formatters\TextFormatter;
use Fi1a\Log\LevelInterface;
use Fi1a\Log\Record;
use InvalidArgumentException;

/**
 * Отправка логов на почту
 */
class MailHandler extends AbstractHandler
{
    public const DEFAULT_SUBJECT_FORMAT = '{{channel}}.{{levelName}}[{{level}}] {{message}} {{context}}';

    /**
     * @var array<int, string>
     */
    protected $to;

    /**
     * @var string
     */
    protected $subjectFormat;

    /**
     * @var string
     */
    protected $from;

    /**
     * @var int
     */
    protected $countInBatch;

    /**
     * @var array<int, Record>
     */
    protected $records = [];

    /**
     * @var string
     */
    protected $contentType = 'text/html';

    /**
     * @var string
     */
    protected $encoding = 'utf-8';

    /**
     * @var array<int, string>
     */
    protected $headers = [];

    /**
     * @var array<int, string>
     */
    protected $parameters = [];

    /**
     * @param string|array<int, string> $to
     * @param int|string|LevelInterface $level
     */
    public function __construct(
        $to,
        string $from,
        int $countInBatch = 0,
        ?string $subjectFormat = null,
        $level = LevelInterface::DEBUG,
        ?FormatterInterface $formatter = null
    ) {
        $this->to = (array) $to;
        if ($subjectFormat === null) {
            $subjectFormat = self::DEFAULT_SUBJECT_FORMAT;
        }
        $this->subjectFormat = $subjectFormat;
        $this->from = $from;
        $this->addHeader('From: ' . $from);
        $this->countInBatch = $countInBatch;

        parent::__construct($level, $formatter);
    }

    /**
     * @inheritDoc
     */
    protected function write(Record $record): bool
    {
        $this->records[] = $record;

        if (!$this->countInBatch || count($this->records) >= $this->countInBatch) {
            $this->send();
        }

        return true;
    }

    /**
     * Отправляет email с логом
     */
    protected function send(): void
    {
        if (!count($this->records)) {
            return;
        }

        $content = '';
        $highestRecord = null;
        foreach ($this->records as $record) {
            $content .= (string) $record->formatted;
            if ($highestRecord === null || $record->level->isHigherThan($highestRecord->level)) {
                $highestRecord = $record;
            }
        }

        $headers = ltrim(implode("\r\n", $this->headers) . "\r\n", "\r\n");
        $headers .= 'Content-type: ' . $this->getContentType() . '; charset=' . $this->getEncoding() . "\r\n";
        if ($this->getContentType() === 'text/html' && strpos($headers, 'MIME-Version:') === false) {
            $headers .= 'MIME-Version: 1.0' . "\r\n";
        }

        $subjectFormatter = new TextFormatter($this->subjectFormat);
        $subject = (string) $subjectFormatter->format($highestRecord);

        $parameters = implode(' ', $this->parameters);
        foreach ($this->to as $to) {
            $this->mail($to, $subject, $content, $headers, $parameters);
        }

        $this->records = [];
    }

    /**
     * Тип контента письма
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * Тип контента письма ('text/html', 'text/plain')
     */
    public function setContentType(string $contentType): self
    {
        if (!$contentType) {
            throw new InvalidArgumentException('$contentType не может быть пустым');
        }

        $this->contentType = $contentType;

        return $this;
    }

    /**
     * Кодировка письма
     */
    public function getEncoding(): string
    {
        return $this->encoding;
    }

    /**
     * Кодировка письма
     */
    public function setEncoding(string $encoding): self
    {
        if (!$encoding) {
            throw new InvalidArgumentException('$encoding не может быть пустым');
        }

        $this->encoding = $encoding;

        return $this;
    }

    /**
     * Добавить заголовок
     */
    public function addHeader(string $header): self
    {
        if (strpos($header, "\n") !== false || strpos($header, "\r") !== false) {
            throw new InvalidArgumentException('Заголовок не может содержать символы новой строки');
        }
        $this->headers[] = $header;

        return $this;
    }

    /**
     * Добавить заголовоки
     *
     * @param array<int, string> $headers
     */
    public function addHeaders(array $headers): self
    {
        foreach ($headers as $header) {
            $this->addHeader($header);
        }

        return $this;
    }

    /**
     * Добавить параметр для функции mail
     *
     * @param array<int, string> $parameters
     */
    public function addParameter(string $parameter): self
    {
        $this->parameters[] = $parameter;

        return $this;
    }

    /**
     * Добавить параметры для функции mail
     *
     * @param array<int, string> $parameters
     */
    public function addParameters(array $parameters): self
    {
        foreach ($parameters as $parameter) {
            $this->addParameter($parameter);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function close(): void
    {
        parent::close();
        $this->send();
    }

    /**
     * Метод mail
     *
     * @codeCoverageIgnore
     */
    protected function mail(string $to, string $subject, string $content, string $headers, string $parameters): bool
    {
        return mail(
            $to,
            $subject,
            $content,
            $headers,
            $parameters
        );
    }
}
