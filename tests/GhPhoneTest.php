<?php

use Nanayawkumi\GhPhoneValidator\GhPhoneValidator;

it('normalizes ghana phone numbers', function () {
    expect(GhPhoneValidator::normalize('241234567'))->toBe('0241234567');
    expect(GhPhoneValidator::normalize('+233241234567'))->toBe('0241234567');
    expect(GhPhoneValidator::normalize('233241234567'))->toBe('0241234567');
    expect(GhPhoneValidator::normalize('024-123-4567'))->toBe('0241234567');
    expect(GhPhoneValidator::normalize('024 123 4567'))->toBe('0241234567');
});

it('detects network correctly', function () {
    $network = GhPhoneValidator::network('0241234567');

    expect($network['slug'])->toBe('mtn');
});

it('formats phone numbers correctly', function () {
    expect(GhPhoneValidator::formatRaw('+233 24 123 4567'))
        ->toBe('0241234567');

    expect(GhPhoneValidator::formatNational('0241234567'))
        ->toBe('024 123 4567');

    expect(GhPhoneValidator::formatInternational('0241234567'))
        ->toBe('+233 24 123 4567');

    expect(GhPhoneValidator::formatE164('0241234567'))
        ->toBe('+233241234567');
});

it('returns null for invalid numbers when formatting', function () {
    expect(GhPhoneValidator::formatE164('123'))->toBeNull();
});

it('rejects unknown prefixes in strict mode', function () {
    config()->set('gh-phone-validator.strict', true);

    expect(GhPhoneValidator::normalize('0291234567'))->toBeNull();
});

it('accepts unknown prefixes when strict mode is disabled', function () {
    config()->set('gh-phone-validator.strict', false);

    expect(GhPhoneValidator::normalize('0291234567'))->toBe('0291234567');
});