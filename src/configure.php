<?php

declare(strict_types=1);

use Fi1a\DI\Builder;
use Fi1a\Log\Channels;
use Fi1a\Log\ChannelsInterface;

di()->config()->addDefinition(
    Builder::build(ChannelsInterface::class)
        ->defineFactory(function () {
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
