<?php

declare(strict_types=1);

namespace Fi1a\Log;

/**
 * Уровень логирования
 */
interface LevelInterface
{
    public const EMERGENCY = 800;

    public const ALERT = 700;

    public const CRITICAL = 600;

    public const ERROR = 500;

    public const WARNING = 400;

    public const NOTICE = 300;

    public const INFO = 200;

    public const DEBUG = 100;

    public function __construct(int $level);

    /**
     * Возвращает уровень логирования
     */
    public function getLevel(): int;
}
