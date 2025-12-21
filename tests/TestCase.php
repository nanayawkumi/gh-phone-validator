<?php

namespace Nanayawkumi\GhPhoneValidator\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Nanayawkumi\GhPhoneValidator\GhPhoneValidatorServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            GhPhoneValidatorServiceProvider::class,
        ];
    }
}