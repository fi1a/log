<?php

declare(strict_types=1);

namespace Fi1a\Log\Handlers;

use Fi1a\Log\Formatters\FormatterInterface;
use Fi1a\Log\LevelInterface;
use Fi1a\Log\Record;
use LogicException;

use function closelog;
use function openlog;

use const LOG_PID;
use const LOG_USER;

/**
 * Логирование в syslog
 */
class SyslogHandler extends AbstractSyslogHandler
{
    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var int
     */
    protected $logPid;

    /**
     * @param int|string $facility
     * @param int|string|LevelInterface $level
     */
    public function __construct(
        string $prefix = '',
        $facility = LOG_USER,
        $level = LevelInterface::DEBUG,
        ?FormatterInterface $formatter = null,
        int $logPid = LOG_PID
    ) {
        $this->prefix = $prefix;
        $this->logPid = $logPid;

        parent::__construct($facility, $level, $formatter);
    }

    /**
     * @inheritDoc
     */
    protected function write(Record $record): bool
    {
        if ($this->openSyslog() === false) {
            throw new LogicException(
                sprintf(
                    'Неудалось открыть syslog ident="%s" и facility="%s"',
                    $this->prefix,
                    $this->facility
                )
            );
        }

        return $this->syslog($record);
    }

    /**
     * @inheritDoc
     */
    public function close(): void
    {
        parent::close();
        $this->closeSyslog();
    }

    /**
     * Запись в syslog
     *
     * @codeCoverageIgnore
     */
    protected function syslog(Record $record): bool
    {
        return syslog($record->level->getRFC5424Value(), (string) $record->formatted);
    }

    /**
     * Открывает syslog
     */
    protected function openSyslog(): bool
    {
        return openlog($this->prefix, $this->logPid, $this->facility);
    }

    /**
     * Закрывает syslog
     */
    protected function closeSyslog(): bool
    {
        return closelog();
    }
}
