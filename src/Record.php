<?php

declare(strict_types=1);

namespace Fi1a\Log;

use DateTime;

/**
 * Запись лога
 */
class Record
{
    /**
     * @var DateTime
     */
    public $datetime;

    /**
     * @var LevelInterface
     */
    public $level;

    /**
     * @var string
     */
    public $channel;

    /**
     * @var string
     */
    public $message;

    /**
     * @var mixed[]
     */
    public $values;

    /**
     * @var mixed[]
     */
    public $context;

    /**
     * @var mixed
     */
    public $formatted;

    /**
     * @inheritDoc
     */
    public function __construct(
        DateTime $datetime,
        LevelInterface $level,
        string $channel,
        string $message,
        array $values = [],
        array $context = [],
        ?string $formatted = null
    ) {
        $this->datetime = $datetime;
        $this->level = $level;
        $this->channel = $channel;
        $this->message = $message;
        $this->values = $values;
        $this->context = $context;
        $this->formatted = $formatted;
    }
}
