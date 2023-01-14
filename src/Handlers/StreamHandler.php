<?php

declare(strict_types=1);

namespace Fi1a\Log\Handlers;

use ErrorException;
use Fi1a\Log\Formatters\FormatterInterface;
use Fi1a\Log\LevelInterface;
use Fi1a\Log\Record;
use InvalidArgumentException;

use const LOCK_EX;
use const LOCK_UN;
use const PATHINFO_DIRNAME;

/**
 * Обработчик логирования в файле (поток)
 */
class StreamHandler extends AbstractHandler
{
    /**
     * @var resource|null
     */
    protected $stream;

    /**
     * @var string|null
     */
    protected $path;

    /**
     * @var int|null
     */
    protected $permission;

    /**
     * @var bool
     */
    protected $lock;

    /**
     * @var string|null
     */
    protected $error;

    /**
     * @param string|resource $stream
     * @param int|string|LevelInterface $level
     */
    public function __construct(
        $stream,
        $level = LevelInterface::DEBUG,
        ?int $permission = null,
        bool $lock = false,
        ?FormatterInterface $formatter = null
    ) {
        if (is_string($stream)) {
            $this->path = $stream;
        }
        if (is_resource($stream)) {
            $this->stream = $stream;
        }
        if (!$this->path && !$this->stream) {
            throw new InvalidArgumentException('Передайте путь к файлу или ресурс для логирования');
        }
        $this->permission = $permission;
        $this->lock = $lock;
        parent::__construct($level, $formatter);
    }

    /**
     * @inheritDoc
     */
    protected function write(Record $record): bool
    {
        if (!is_resource($this->stream) && $this->path) {
            $this->error = null;
            $dirName = pathinfo($this->path, PATHINFO_DIRNAME);
            set_error_handler([$this, 'errorHandler']);
            if ($this->createDir($dirName) === false) {
                throw new ErrorException(
                    sprintf(
                        'Не удалось создать директорию "%s" (%s)',
                        $dirName,
                        (string) $this->error
                    )
                );
            }
            $stream = fopen($this->path, 'a');
            if ($this->permission !== null) {
                @chmod($this->path, $this->permission);
            }
            restore_error_handler();
            if ($this->isResource($stream) === false) {
                throw new ErrorException(
                    sprintf(
                        'Не удалось открыть поток "%s" (%s)',
                        $this->path,
                        (string) $this->error
                    )
                );
            }
            $this->stream = $stream;
        }

        /** @var resource $stream */
        $stream = $this->stream;

        if ($this->lock) {
            @flock($stream, LOCK_EX);
        }
        fwrite($stream, (string) $record->formatted);
        if ($this->lock) {
            @flock($stream, LOCK_UN);
        }

        return true;
    }

    /**
     * Создание директории
     */
    protected function createDir(string $dirName): bool
    {
        return is_dir($dirName)
            || mkdir($dirName, 0777, true) !== false;
    }

    /**
     * Удалось открыть поток или нет
     *
     * @param mixed $stream
     */
    protected function isResource($stream): bool
    {
        return is_resource($stream);
    }

    /**
     * Обработчик ошибки
     *
     * @codeCoverageIgnore
     */
    protected function errorHandler(int $code, string $message): bool
    {
        $this->error = preg_replace('{^(fopen|mkdir)\(.*?\): }', '', $message);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function close(): void
    {
        parent::close();
        if ($this->path !== null && is_resource($this->stream)) {
            /** @psalm-suppress InvalidPropertyAssignmentValue */
            fclose($this->stream);
        }
        $this->stream = null;
    }
}
