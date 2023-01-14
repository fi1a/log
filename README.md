# PHP библиотека ведения журнала (логирования)

[![Latest Version][badge-release]][packagist]
[![Software License][badge-license]][license]
[![PHP Version][badge-php]][php]
![Coverage Status][badge-coverage]
[![Total Downloads][badge-downloads]][downloads]
[![Support mail][badge-mail]][mail]

Данный пакет предоставляет функционал ведения журнала (логирования), используя обработчики. На данный момент доступны обработчики
записи в файл, syslog и отправка записей на email адрес. Наличие "каналов" позволяет вести журнал, отправляя записи в
разные обработчики с разными настройками. Один канал может содержать множество обработчиков записей журнала.
Сообщения журнала могут быть записаны в разные обработчики в зависимости от их серьезности (уровня).

## Установка

Установить этот пакет можно как зависимость, используя Composer.

``` bash
composer require fi1a/log
```

## Хелпер

В пакете доступен хелпер:

logger(?string $channel = null): ChannelsInterface|LoggerInterface|false;

Он возвращает каналы логгера.

Используя канал логирования `channel1`, записать в лог критическую ошибку:

```php
logger('channel1')->critical('Critical error with {{value}}', ['value' => 'message',], ['id' => 1]);
```

## Конфигурирование

Конфигурирование канала осуществляется путем добавления экземпляра класса `Fi1a\Log\Logger`, используя метод
`addChannel` класса `Fi1a\Log\ChannelsInterface` возвращаемого хелпером `logger` без передачи аргументов.

Обработчики добавляются методом `addHandler` класса `Fi1a\Log\Logger`. В примере добавляются обработчики
для записи в файл журнала `Fi1a\Log\Handlers\StreamHandler` записей с уровнем `Fi1a\Log\LevelInterface::ERROR` и
отправку на email записей с уровнем `Fi1a\Log\LevelInterface::EMERGENCY` с помощью обработчика
`Fi1a\Log\Handlers\MailHandler`.

```php
use Fi1a\Log\Handlers\MailHandler;
use Fi1a\Log\Handlers\StreamHandler;
use Fi1a\Log\LevelInterface;
use Fi1a\Log\Logger;

$logger = new Logger('channel1');

$logger->addHandler(new StreamHandler(__DIR__ . '/filelog.log', LevelInterface::ERROR));
$logger->addHandler(new MailHandler('to@fi1a.ru', 'from@fila.ru', LevelInterface::EMERGENCY));

logger()->addChannel($logger);
```

## Доступные обработчики

- `Fi1a\Log\Handlers\StreamHandler` - обработчик логирования в файл (поток);
- `Fi1a\Log\Handlers\RotatingFileHandler` - сохраняет логи в файлах с именем по маске времени. Хранится ограниченное кол-во файлов;
- `Fi1a\Log\Handlers\SyslogHandler` - логирование в syslog;
- `Fi1a\Log\Handlers\MailHandler` - отправка логов на почту.

### Fi1a\Log\Handlers\StreamHandler

Сохранение записей журнала в файл или поток.

| Аргументы конструктора                                     | Описание                                           |
|------------------------------------------------------------|----------------------------------------------------|
| string, resource $stream                                   | Путь к файлу или поток                             |
| int, string, LevelInterface $level = LevelInterface::DEBUG | Уровень от которого обработчик записывает в журнал |
| ?int $permission = null                                    | Права на файл                                      |
| bool $lock = false                                         | Блокировать файл при записи                        |
| ?FormatterInterface $formatter = null                      | Объект форматирования записей журнала              |

```php
use Fi1a\Log\Handlers\StreamHandler;

$handler = new StreamHandler(__DIR__ . '/filelog.log', LevelInterface::ERROR);
$logger->addHandler($handler);
```

### Fi1a\Log\Handlers\RotatingFileHandler

Сохранение записей журнала в файлах с именем по маске времени. Хранится ограниченное кол-во файлов.

| Аргументы конструктора                                     | Описание                                            |
|------------------------------------------------------------|-----------------------------------------------------|
| string $filePathAndName                                    | Путь и название файла ведения журнала               |
| int $maxFiles = 0                                          | Кол-во файлов, которые хранятся. 0 - не ограничено. |
| ?string $dateFormat = null                                 | Формат даты и времени в названии файла              |
| int, string, LevelInterface $level = LevelInterface::DEBUG | Уровень от которого обработчик записывает в журнал  |
| ?int $permission = null                                    | Права на файл                                       |
| bool $lock = false                                         | Блокировать файл при записи                         |
| ?FormatterInterface $formatter = null                      | Объект форматирования записей журнала               |

```php
use Fi1a\Log\Handlers\RotatingFileHandler;

$handler = new RotatingFileHandler(__DIR__ . '/filelog.log', 2, 'Y-m-d', LevelInterface::ERROR);
$logger->addHandler($handler);
```

### Fi1a\Log\Handlers\SyslogHandler

Сохранение записей журнала, используя syslog.

| Аргументы конструктора                                     | Описание                                                             |
|------------------------------------------------------------|----------------------------------------------------------------------|
| string $prefix = ''                                        | Строка добавляется к каждому сообщению                               |
| $facility = LOG_USER                                       | Параметр facility используется для определения типа программы        |
| int, string, LevelInterface $level = LevelInterface::DEBUG | Уровень от которого обработчик записывает в журнал                   |
| ?FormatterInterface $formatter = null                      | Объект форматирования записей журнала                                |
| int $logPid = LOG_PID                                      | Аргумент для указания используемых опций при создании записи журнала |

```php
use Fi1a\Log\Handlers\SyslogHandler;

$handler = new SyslogHandler('error', LOG_USER, LevelInterface::ERROR);
$logger->addHandler($handler);
```

### Fi1a\Log\Handlers\MailHandler

Отправка записей журнала на почту.

| Аргументы конструктора                                     | Описание                                                                    |
|------------------------------------------------------------|-----------------------------------------------------------------------------|
| string, array<int, string> $to                             | Список email адресов получателей                                            |
| string $from                                               | Email адрес отправителя                                                     |
| int $countInBatch = 0                                      | Кол-во отправляемых записей. 0 - не накапливать записи, отправлять по одной |
| ?string $subjectFormat = null                              | Формат заголовка письма                                                     |
| int, string, LevelInterface $level = LevelInterface::DEBUG | Уровень от которого обработчик записывает в журнал                          |
| ?FormatterInterface $formatter = null                      | Объект форматирования записей журнала                                       |

```php
use Fi1a\Log\Handlers\MailHandler;

$handler = new MailHandler('to@fi1a.ru', 'from@fila.ru', LevelInterface::ERROR);
$logger->addHandler($handler);
```

## Запись сообщений журнала

Вы можете записывать информацию в журнал с помощью канала возвращаемого хелпером `logger`,
который при передаче аргумента с названием канала возвращает его.
Средство ведения журнала обеспечивает восемь уровней: emergency, alert, critical, error, warning, notice, info, и debug.
Для каждого уровня есть свой метод записи в журнал:

```php
$logger = logger('channel1');

$logger->emergency('Log {{value}}', ['value' => 'message'], ['id' => 1]);
$logger->alert('Log {{value}}', ['value' => 'message'], ['id' => 1]);
$logger->critical('Log {{value}}', ['value' => 'message'], ['id' => 1]);
$logger->error('Log {{value}}', ['value' => 'message'], ['id' => 1]);
$logger->warning('Log {{value}}', ['value' => 'message'], ['id' => 1]);
$logger->notice('Log {{value}}', ['value' => 'message'], ['id' => 1]);
$logger->info('Log {{value}}', ['value' => 'message'], ['id' => 1]);
$logger->debug('Log {{value}}', ['value' => 'message'], ['id' => 1]);
```

- `emergency` - система не работает;
- `alert` -  ошибка. Необходимо принять меры немедленно.
- `critical` - критическая ошибка;
- `error` - ошибка, которая не требует немедленных действий, но должна быть записана;
- `warning` - предупреждение;
- `notice` - все нормально, но событие значимое;
- `info` - какое-либо событие;
- `debug` - отладочная информация.

Также доступен метод `log` для записи в журнал с передачей уровня логирования в аргументе:

```php
$logger = logger('channel1');

$logger->log(LevelInterface::ERROR, 'Log {{value}}', ['value' => 'message'], ['id' => 1]);
```

## Контекстная информация

Методом `withContext` класса `Fi1a\Log\Logger` (для этого канала), или класса `Fi1a\Log\Channels` (для всех каналов)
может быть передан массив контекстных данных, которые будут включены во все последующие записи журнала.
Также контекстные данные можно передать третьим аргументом любого из методов записи в журнал,
эти контектсные данные будут использованы только к этой записи.

```php
// Глобально для всех каналов
logger()->withContext(['pid' => 1]);

// Для конкретного канала
$logger = logger('channel1');
$logger->withContext(['uid' => 2]);

// Для конкретной записи
$logger->error('Log {{value}}', ['value' => 'message'], ['id' => 1]);

/*
14.01.2023 10:18:58
channel1.ERROR[500]
Log message
{"pid":1,"uid":2,"id":1}
 */
```

## Форматирование

Доступны следующие классы для форматирования сообщений журнала:

- `Fi1a\Log\Formatters\TextFormatter` - форматирование записи лога в текст;
- `Fi1a\Log\Formatters\HtmlFormatter` - форматирование записи лога в виде html.

Каждому обработчику можно передать объект форматирование записи журнала в конструктор.

```php
use Fi1a\Log\Formatters\HtmlFormatter;
use Fi1a\Log\Handlers\StreamHandler;
use Fi1a\Log\LevelInterface;

$formatter = new HtmlFormatter();

$logger->addHandler(
    new StreamHandler(
        __DIR__ . '/log.html',
        LevelInterface::DEBUG,
        0776,
        false,
        $formatter
    )
);

logger()->addChannel($logger);

$logger->error('Log {{value}}', ['value' => 'message'], ['id' => 1]);
```

[badge-release]: https://img.shields.io/packagist/v/fi1a/log?label=release
[badge-license]: https://img.shields.io/github/license/fi1a/log?style=flat-square
[badge-php]: https://img.shields.io/packagist/php-v/fi1a/log?style=flat-square
[badge-coverage]: https://img.shields.io/badge/coverage-100%25-green
[badge-downloads]: https://img.shields.io/packagist/dt/fi1a/log.svg?style=flat-square&colorB=mediumvioletred
[badge-mail]: https://img.shields.io/badge/mail-support%40fi1a.ru-brightgreen

[packagist]: https://packagist.org/packages/fi1a/log
[license]: https://github.com/fi1a/log/blob/master/LICENSE
[php]: https://php.net
[downloads]: https://packagist.org/packages/fi1a/log
[mail]: mailto:support@fi1a.ru