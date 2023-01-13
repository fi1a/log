<?php

declare(strict_types=1);

namespace Fi1a\Log\Handlers;

use Fi1a\Log\Formatters\FormatterInterface;
use Fi1a\Log\Formatters\TextFormatter;
use Fi1a\Log\LevelInterface;
use UnexpectedValueException;

use const LOG_AUTH;
use const LOG_AUTHPRIV;
use const LOG_CRON;
use const LOG_DAEMON;
use const LOG_KERN;
use const LOG_LOCAL0;
use const LOG_LOCAL1;
use const LOG_LOCAL2;
use const LOG_LOCAL3;
use const LOG_LOCAL4;
use const LOG_LOCAL5;
use const LOG_LOCAL6;
use const LOG_LOCAL7;
use const LOG_LPR;
use const LOG_MAIL;
use const LOG_NEWS;
use const LOG_SYSLOG;
use const LOG_USER;
use const LOG_UUCP;

/**
 * Syslog
 */
abstract class AbstractSyslogHandler extends AbstractHandler
{
    /**
     * @var int
     */
    protected $facility;

    /**
     * @var array<string, int>
     */
    protected $facilities = [
        'auth'     => LOG_AUTH,
        'authpriv' => LOG_AUTHPRIV,
        'cron'     => LOG_CRON,
        'daemon'   => LOG_DAEMON,
        'kern'     => LOG_KERN,
        'lpr'      => LOG_LPR,
        'mail'     => LOG_MAIL,
        'news'     => LOG_NEWS,
        'syslog'   => LOG_SYSLOG,
        'user'     => LOG_USER,
        'uucp'     => LOG_UUCP,
    ];

    /**
     * @param int|string $facility
     * @param int|string|LevelInterface $level
     */
    public function __construct(
        $facility = LOG_USER,
        $level = LevelInterface::DEBUG,
        ?FormatterInterface $formatter = null
    ) {
        $this->facilities['local0'] = 128; // LOG_LOCAL0
        $this->facilities['local1'] = 136; // LOG_LOCAL1
        $this->facilities['local2'] = 144; // LOG_LOCAL2
        $this->facilities['local3'] = 152; // LOG_LOCAL3
        $this->facilities['local4'] = 160; // LOG_LOCAL4
        $this->facilities['local5'] = 168; // LOG_LOCAL5
        $this->facilities['local6'] = 176; // LOG_LOCAL6
        $this->facilities['local7'] = 184; // LOG_LOCAL7

        if (!defined('PHP_WINDOWS_VERSION_BUILD')) {
            $this->facilities['local0'] = LOG_LOCAL0;
            $this->facilities['local1'] = LOG_LOCAL1;
            $this->facilities['local2'] = LOG_LOCAL2;
            $this->facilities['local3'] = LOG_LOCAL3;
            $this->facilities['local4'] = LOG_LOCAL4;
            $this->facilities['local5'] = LOG_LOCAL5;
            $this->facilities['local6'] = LOG_LOCAL6;
            $this->facilities['local7'] = LOG_LOCAL7;
        }

        if (is_string($facility) && array_key_exists(mb_strtolower($facility), $this->facilities)) {
            $facility = $this->facilities[mb_strtolower($facility)];
        } elseif (!in_array($facility, array_values($this->facilities), true)) {
            throw new UnexpectedValueException(
                sprintf('Неизвестное значение $facility=%d', $facility)
            );
        }

        $this->facility = $facility;

        parent::__construct($level, $formatter);
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultFormatter(): FormatterInterface
    {
        return new TextFormatter("{{channel}}.{{levelName}}[{{level}}] {{message}} {{context}}\n");
    }
}
