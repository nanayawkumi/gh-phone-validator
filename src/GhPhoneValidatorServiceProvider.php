<?php

namespace Nanayawkumi\GhPhoneValidator;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class GhPhoneValidatorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/gh-phone-validator.php',
            'gh-phone-validator'
        );
    }

    public function boot(): void
    {

        Validator::extend('gh_phone', function ($attribute, $value) {
            return GhPhoneValidator::normalize((string) $value) !== null;
        });

        Validator::replacer('gh_phone', function ($message, $attribute) {
            return "The {$attribute} must be a valid Ghana phone number.";
        });

        $this->publishes([
            __DIR__ . '/../config/gh-phone-validator.php' => config_path('gh-phone-validator.php'),
        ], 'gh-phone-validator-config');
    }
}