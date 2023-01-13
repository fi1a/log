<?php

declare(strict_types=1);

use Fi1a\DI\Builder;
use Fi1a\Log\Channels;
use Fi1a\Log\ChannelsInterface;
use Fi1a\Log\LoggerInterface;

di()->config()->addDefinition(
    Builder::build(ChannelsInterface::class)
        ->defineFactory(function (): ChannelsInterface {
            /** @var ChannelsInterface|null $channels */
            static $channels = null;
            if (is_null($channels)) {
                // @codeCoverageIgnoreStart
                $channels = new Channels();
                // @codeCoverageIgnoreEnd
            }

            return $channels;
        })
        ->getDefinition()
);

di()->config()->addDefinition(
    Builder::build(LoggerInterface::class)
        ->defineFactory(function (): LoggerInterface {
            /** @var ChannelsInterface $channels */
            $channels = di()->get(ChannelsInterface::class);
            /** @var LoggerInterface $logger */
            $logger = $channels->channel();

            return $logger;
        })
    ->getDefinition()
);
