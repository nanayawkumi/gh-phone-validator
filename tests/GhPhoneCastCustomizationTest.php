<?php

use Illuminate\Database\Eloquent\Model;
use Nanayawkumi\GhPhoneValidator\Casts\GhPhoneCast;

class RawPhoneUser extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    protected $casts = [
        'phone' => GhPhoneCast::class . ':raw',
    ];
}

class E164PhoneUser extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    protected $casts = [
        'phone' => GhPhoneCast::class . ':e164',
    ];
}

it('stores phone as raw when configured', function () {
    $user = new RawPhoneUser();
    $user->phone = '+233241234567';

    expect($user->getAttributes()['phone'])
        ->toBe('0241234567');
});

it('stores phone as e164 when configured', function () {
    $user = new E164PhoneUser();
    $user->phone = '0241234567';

    expect($user->getAttributes()['phone'])
        ->toBe('+233241234567');
});