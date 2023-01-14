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

    /**
     * Возвращает уровень логирования
     */
    public function getValue(): int;

    /**
     * Возвращает RFC 5424 значение логирования
     */
    public function getRFC5424Value(): int;

    /**
     * Возвращает название уровня логирования
     */
    public function getName(): string;

    /**
     * Возвращает true, если уровень меньше или равен переданному
     */
    public function includes(LevelInterface $level): bool;

    /**
     * Возвращает true, если уровень выше переданного
     */
    public function isHigherThan(LevelInterface $level): bool;

    /**
     * Возвращает true, если уровень меньше переданного
     */
    public function isLowerThan(LevelInterface $level): bool;

    /**
     * Создать объект на основе значения
     */
    public static function fromValue(int $level): self;

    /**
     * Создать объект на основе имени
     */
    public static function fromName(string $levelName): self;

    /**
     * Создать объект на основе имени или значения
     *
     * @param string|int $level
     */
    public static function from($level): self;
}
