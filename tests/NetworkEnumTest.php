<?php

use Nanayawkumi\GhPhoneValidator\Enums\Network;

it('detects network using enum', function () {
    $network = Network::fromPhone('0241234567');

    expect($network)
        ->toBe(Network::MTN)
        ->and($network->label())
        ->toBe('MTN')
        ->and($network->slug())
        ->toBe('mtn');
});

it('returns null for unknown network', function () {
    expect(Network::fromPhone('0291234567'))->toBeNull();
});