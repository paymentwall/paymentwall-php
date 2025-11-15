<?php

use Behat\Config\Config;
use Behat\Config\Profile;
use Behat\Config\Suite;
use Paymentwall;

$profile = (new Profile('default'))
    ->withSuite(
        (new Suite('core_features'))
            ->withPaths(
                '%paths.base%/features',
            )->withContexts(
                Paymentwall\ChargeContext::class,
                Paymentwall\FeatureContext::class,
                Paymentwall\PingbackContext::class,
                Paymentwall\WidgetContext::class
            )
    )
;

return (new Config())
    ->withProfile($profile)
    ;
