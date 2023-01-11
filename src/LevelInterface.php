<?php

namespace Fi1a\Log;

/**
 * Уровень логирования
 */
interface LevelInterface
{
    const EMERGENCY = 800;

    const ALERT = 700;

    const CRITICAL = 600;

    const ERROR = 500;

    const WARNING = 400;

    const NOTICE = 300;

    const INFO = 200;

    const DEBUG = 100;

    public function __construct(int $level);

    /**
     * Возвращает уровень логирования
     */
    public function getLevel(): int;
}
