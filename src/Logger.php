<?php

declare(strict_types=1);

namespace Fi1a\Log;

use DateTime;
use Fi1a\Log\Handlers\HandlerInterface;

/**
 * Логгер
 */
class Logger implements LoggerInterface
{
    /**
     * @var string
     */
    private $channel;

    /**
     * @var HandlerInterface[]
     */
    private $handlers = [];

    /**
     * @var mixed[]
     */
    private $context = [];

    public function __construct(string $channel)
    {
        $this->channel = $channel;
    }

    /**
     * @inheritDoc
     */
    public function emergency(string $message, array $values = [], array $context = []): bool
    {
        return $this->log(LevelInterface::EMERGENCY, $message, $values, $context);
    }

    /**
     * @inheritDoc
     */
    public function alert(string $message, array $values = [], array $context = []): bool
    {
        return $this->log(LevelInterface::ALERT, $message, $values, $context);
    }

    /**
     * @inheritDoc
     */
    public function critical(string $message, array $values = [], array $context = []): bool
    {
        return $this->log(LevelInterface::CRITICAL, $message, $values, $context);
    }

    /**
     * @inheritDoc
     */
    public function error(string $message, array $values = [], array $context = []): bool
    {
        return $this->log(LevelInterface::ERROR, $message, $values, $context);
    }

    /**
     * @inheritDoc
     */
    public function warning(string $message, array $values = [], array $context = []): bool
    {
        return $this->log(LevelInterface::WARNING, $message, $values, $context);
    }

    /**
     * @inheritDoc
     */
    public function notice(string $message, array $values = [], array $context = []): bool
    {
        return $this->log(LevelInterface::NOTICE, $message, $values, $context);
    }

    /**
     * @inheritDoc
     */
    public function info(string $message, array $values = [], array $context = []): bool
    {
        return $this->log(LevelInterface::INFO, $message, $values, $context);
    }

    /**
     * @inheritDoc
     */
    public function debug(string $message, array $values = [], array $context = []): bool
    {
        return $this->log(LevelInterface::DEBUG, $message, $values, $context);
    }

    /**
     * @inheritDoc
     */
    public function log($level, string $message, array $values = [], array $context = []): bool
    {
        $record = new Record(
            new DateTime(),
            $level instanceof LevelInterface ? $level : Level::from($level),
            $this->channel,
            $message,
            $values,
            array_replace_recursive($this->context, $context)
        );
        $result = false;
        foreach ($this->handlers as $handler) {
            if ($handler->handle($record) === true) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function withContext(array $context): bool
    {
        $this->context = array_replace_recursive($this->context, $context);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function addHandler(HandlerInterface $handler): bool
    {
        $this->handlers[] = $handler;

        return true;
    }

    /**
     * @inheritDoc
     */
    public function setHandlers(array $handlers): bool
    {
        $this->handlers = [];
        foreach ($handlers as $handler) {
            $this->addHandler($handler);
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function getHandlers(): array
    {
        return $this->handlers;
    }

    /**
     * @inheritDoc
     */
    public function getChannel(): string
    {
        return $this->channel;
    }
}
