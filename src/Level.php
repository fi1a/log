<?php

declare(strict_types=1);

namespace Fi1a\Log;

/**
 * Уровень логирования
 */
class Level implements LevelInterface
{
    /**
     * @var int
     */
    private $level;

    public function __construct(int $level)
    {
        $this->level = $level;
    }

    /**
     * @inheritDoc
     */
    public function getLevel(): int
    {
        return $this->level;
    }
}
