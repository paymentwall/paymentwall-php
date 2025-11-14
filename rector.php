<?php

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths(
        array_merge(
            ['features', 'lib'],
        )
    )
    ->withRootFiles()
    ->withIndent()
    ->withPhpSets(php84: true)
    ;
