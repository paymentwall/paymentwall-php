<?php

use Behat\Config\Config;
use Behat\Config\Profile;
use Behat\Config\Suite;

$profile = (new Profile('default'))
    ->withSuite(
        (new Suite('core_features'))
            ->withPaths(
                '%paths.base%/features',
            )->withContexts(
                ChargeContext::class,
                FeatureContext::class,
                PingbackContext::class,
                WidgetContext::class
            )
    )
;

return (new Config())
    ->withProfile($profile)
    ;
