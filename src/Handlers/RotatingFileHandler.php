<?php

declare(strict_types=1);

namespace Fi1a\Log\Handlers;

use DateTime;
use Fi1a\Log\Formatters\FormatterInterface;
use Fi1a\Log\LevelInterface;
use Fi1a\Log\Record;
use InvalidArgumentException;

/**
 * Сохраняет логи в файлах с именем по маске времени. Хранится ограниченное кол-во файлов
 */
class RotatingFileHandler extends StreamHandler
{
    public const PER_HOUR = 'Y-m-d-H';
    public const PER_DAY = 'Y-m-d';
    public const PER_MONTH = 'Y-m';
    public const PER_YEAR = 'Y';

    /**
     * @var string
     */
    protected $filePathAndName;

    /**
     * @var int
     */
    protected $maxFiles;

    /**
     * @var string
     */
    protected $fileNameFormat;

    /**
     * @var string
     */
    protected $dateFormat;

    /**
     * @var bool|null
     */
    protected $rotate;

    /**
     * @var DateTime
     */
    protected $next;

    /**
     * @param string|resource $stream
     * @param int|string|LevelInterface $level
     */
    public function __construct(
        string $filePathAndName,
        int $maxFiles = 0,
        ?string $dateFormat = null,
        $level = LevelInterface::DEBUG,
        ?int $permission = null,
        bool $lock = false,
        ?FormatterInterface $formatter = null
    ) {
        if (!$filePathAndName) {
            throw new InvalidArgumentException('Имя файла не может быть пустым');
        }
        $this->filePathAndName = $filePathAndName;
        $this->maxFiles = $maxFiles;
        $this->fileNameFormat = '{{filename}}-{{date}}';
        if ($dateFormat === null) {
            $dateFormat = self::PER_DAY;
        }
        $this->dateFormat = $dateFormat;
        $this->next = new DateTime('tomorrow');

        parent::__construct($this->getFileName(), $level, $permission, $lock, $formatter);
    }

    /**
     * Возвращает название и путь к файлу
     */
    protected function getFileName(): string
    {
        $pathInfo = pathinfo($this->filePathAndName);
        $fileName = str_replace(
            [
                '{{filename}}', '{{date}}',
            ],
            [
                $pathInfo['filename'],
                date($this->dateFormat),
            ],
            $pathInfo['dirname'] . '/' . $this->fileNameFormat
        );

        if (isset($pathInfo['extension'])) {
            $fileName .= '.' . $pathInfo['extension'];
        }

        return $fileName;
    }

    /**
     * Шаблон для glob
     */
    protected function getFileNamePattern(): string
    {
        $pathInfo = pathinfo($this->filePathAndName);
        $datePattern = str_replace(
            ['Y', 'y', 'm', 'd', 'H',],
            ['[0-9][0-9][0-9][0-9]', '[0-9][0-9]', '[0-9][0-9]', '[0-9][0-9]', '[0-9][0-9]',],
            $this->dateFormat
        );

        $pattern = str_replace(
            [
                '{{filename}}', '{{date}}',
            ],
            [
                $pathInfo['filename'],
                $datePattern,
            ],
            $pathInfo['dirname'] . '/' . $this->fileNameFormat
        );
        if (isset($pathInfo['extension'])) {
            $pattern .= '.' . $pathInfo['extension'];
        }

        return $pattern;
    }

    /**
     * @inheritDoc
     */
    protected function write(Record $record): bool
    {
        $this->rotate = $this->isMustRotate($record);

        $return = parent::write($record);

        if ($this->rotate) {
            $this->rotate();
        }

        return $return;
    }

    /**
     * Нужна ротация или нет
     */
    protected function isMustRotate(Record $record): bool
    {
        $rotate = false;
        if ($this->rotate === null) {
            $rotate = $this->stream === null || ($this->path && !file_exists($this->path));
        }
        if ($this->next <= $record->datetime) {
            $rotate = true;
        }

        return $rotate;
    }

    /**
     * Ротирует файлы
     */
    protected function rotate(): void
    {
        if ($this->path !== $this->getFileName()) {
            parent::close();
        }

        $this->path = $this->getFileName();
        $this->next = new DateTime('tomorrow');
        $this->rotate = false;

        if ($this->maxFiles === 0) {
            return;
        }

        $logFiles = $this->glob();
        if ($logFiles === false) {
            return;
        }

        if ($this->maxFiles >= count($logFiles)) {
            return;
        }

        usort($logFiles, function (string $file1, string $file2) {
            return strcmp($file2, $file1);
        });

        foreach (array_slice($logFiles, $this->maxFiles) as $file) {
            if (!$this->isWritable($file)) {
                continue;
            }
            unlink($file);
        }
    }

    /**
     * Файлы по маске
     *
     * @return string[]|false
     */
    protected function glob()
    {
        return glob($this->getFileNamePattern());
    }

    /**
     * Есть ли доступ на запись
     */
    protected function isWritable(string $file): bool
    {
        return is_writable($file);
    }
}
