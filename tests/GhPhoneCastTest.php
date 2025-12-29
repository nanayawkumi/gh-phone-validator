<?php

use Illuminate\Database\Eloquent\Model;
use Nanayawkumi\GhPhoneValidator\Casts\GhPhoneCast;

class TestUser extends Model
{
    protected $table = 'users';
    protected $guarded = [];
    public $timestamps = false;

    protected $casts = [
        'phone' => GhPhoneCast::class,
    ];
}

it('casts phone number to value object that works as string', function () {
    $user = new TestUser(['phone' => '+233241234567']);

    // When used as string, returns raw format
    expect((string) $user->phone)
        ->toBe('0241234567')
        ->and($user->phone)
        ->toBeInstanceOf(\Nanayawkumi\GhPhoneValidator\ValueObjects\PhoneNumber::class);
});

it('allows formatting via method calls', function () {
    $user = new TestUser(['phone' => '+233241234567']);

    expect($user->phone->e164())
        ->toBe('+233241234567')
        ->and($user->phone->national())
        ->toBe('024 123 4567')
        ->and($user->phone->international())
        ->toBe('+233 24 123 4567');
});

it('allows method call syntax via __invoke', function () {
    $user = new TestUser(['phone' => '+233241234567']);

    expect($user->phone()->e164())
        ->toBe('+233241234567')
        ->and($user->phone()->national())
        ->toBe('024 123 4567');
});

it('stores phone number in raw format by default', function () {
    $user = new TestUser();
    $user->phone = '024 123 4567';

    expect($user->getAttributes()['phone'])
        ->toBe('0241234567');
});

it('converts e164 format to raw when retrieving', function () {
    // Simulate a value stored as E.164 (from old data or explicit :e164 cast)
    $user = new TestUser();
    $user->setRawAttributes(['phone' => '+233241234567']);

    expect((string) $user->phone)
        ->toBe('0241234567');
});
