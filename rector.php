<?php

declare(strict_types=1);

use Contao\Rector\Set\SetList;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/src',
        __DIR__.'/contao',
    ])
    ->withSets([SetList::CONTAO])
    // Adjust the configuration according to your needs.
;
