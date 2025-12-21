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