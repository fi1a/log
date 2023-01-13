<?php

declare(strict_types=1);

namespace Fi1a\Log;

use InvalidArgumentException;

/**
 * Уровень логирования
 */
class Level implements LevelInterface
{
    /**
     * @var array<int, string>
     */
    private static $names = [
        LevelInterface::EMERGENCY => 'EMERGENCY',
        LevelInterface::ALERT => 'ALERT',
        LevelInterface::CRITICAL => 'CRITICAL',
        LevelInterface::ERROR => 'ERROR',
        LevelInterface::WARNING => 'WARNING',
        LevelInterface::NOTICE => 'NOTICE',
        LevelInterface::INFO => 'INFO',
        LevelInterface::DEBUG => 'DEBUG',
    ];

    /**
     * @var array<int, int>
     */
    protected static $rfc5424 = [
        LevelInterface::EMERGENCY => 0,
        LevelInterface::ALERT => 1,
        LevelInterface::CRITICAL => 2,
        LevelInterface::ERROR => 3,
        LevelInterface::WARNING => 4,
        LevelInterface::NOTICE => 5,
        LevelInterface::INFO => 6,
        LevelInterface::DEBUG => 7,
    ];

    /**
     * @var int
     */
    private $level;

    protected function __construct(int $level)
    {
        if (!array_key_exists($level, static::$names)) {
            throw new InvalidArgumentException(sprintf('Неизвестный уровень логирования %d', $level));
        }

        $this->level = $level;
    }

    /**
     * @inheritDoc
     */
    public function getValue(): int
    {
        return $this->level;
    }

    /**
     * @inheritDoc
     */
    public function getRFC5424Value(): int
    {
        return static::$rfc5424[$this->level];
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return static::$names[$this->level];
    }

    /**
     * @inheritDoc
     */
    public function includes(LevelInterface $level): bool
    {
        return $this->getValue() <= $level->getValue();
    }

    /**
     * @inheritDoc
     */
    public static function fromValue(int $level): LevelInterface
    {
        return new self($level);
    }

    /**
     * @inheritDoc
     */
    public static function fromName(string $levelName): LevelInterface
    {
        $levelName = mb_strtoupper($levelName);
        $level = array_search($levelName, static::$names);
        if ($level === false) {
            throw new InvalidArgumentException(
                sprintf('Неизвестный уровень логирования "%s"', $levelName)
            );
        }

        return new self($level);
    }

    /**
     * @inheritDoc
     */
    public static function from($level): LevelInterface
    {
        if (is_numeric($level)) {
            return static::fromValue((int) $level);
        }

        return static::fromName($level);
    }
}
