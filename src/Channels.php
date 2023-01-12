<?php

declare(strict_types=1);

namespace Fi1a\Log;

/**
 * Каналы логирования
 */
class Channels implements ChannelsInterface
{
    /**
     * @var LoggerInterface[]
     */
    private $channels = [];

    /**
     * @var mixed[]
     */
    private $context = [];

    /**
     * @inheritDoc
     */
    public function withContext(array $context): bool
    {
        $this->context = array_replace_recursive($this->context, $context);
        foreach ($this->channels as $logger) {
            $logger->withContext($this->context);
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function channel(?string $channel = null)
    {
        if (!$channel) {
            $channel = 'default';
        }

        if (!$this->hasChannel($channel)) {
            return false;
        }

        return $this->channels[mb_strtolower($channel)];
    }

    /**
     * @inheritDoc
     */
    public function addChannel(LoggerInterface $logger): bool
    {
        $logger->withContext($this->context);
        $this->channels[mb_strtolower($logger->getChannel())] = $logger;

        return true;
    }

    /**
     * @inheritDoc
     */
    public function hasChannel(string $channel): bool
    {
        return array_key_exists(mb_strtolower($channel), $this->channels);
    }

    /**
     * @inheritDoc
     */
    public function deleteChannel(string $channel): bool
    {
        if (!$this->hasChannel($channel)) {
            return false;
        }

        unset($this->channels[mb_strtolower($channel)]);

        return true;
    }
}
