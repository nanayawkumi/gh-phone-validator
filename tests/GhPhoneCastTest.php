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

it('casts phone number to value object', function () {
    $user = new TestUser(['phone' => '+233241234567']);

    expect($user->phone)
        ->toBeInstanceOf(\Nanayawkumi\GhPhoneValidator\ValueObjects\PhoneNumber::class)
        ->and($user->phone->national())
        ->toBe('024 123 4567');
});

it('stores phone number in e164 format', function () {
    $user = new TestUser();
    $user->phone = '024 123 4567';

    expect($user->getAttributes()['phone'])
        ->toBe('+233241234567');
});