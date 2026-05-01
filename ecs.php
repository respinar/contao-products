<?php

declare(strict_types=1);

use Contao\EasyCodingStandard\Set\SetList;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withSets([SetList::CONTAO])
    // Adjust the configuration according to your needs.
    ->withPaths([
        __DIR__.'/src/',
        __DIR__.'/contao/',
    ])
;
