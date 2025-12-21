<?php

namespace Nanayawkumi\GhPhoneValidator\Enums;

enum Network: string
{
    case MTN = 'mtn';
    case TELECEL = 'telecel';
    case AIRTELTIGO = 'airteltigo';

    public function label(): string
    {
        return match ($this) {
            self::MTN => 'MTN',
            self::TELECEL => 'Telecel',
            self::AIRTELTIGO => 'AirtelTigo',
        };
    }

    public function slug(): string
    {
        return $this->value;
    }

    public function codes(): array
    {
        return config("gh-phone-validator.networks.{$this->value}.codes", []);
    }

    public static function fromPhone(string $phone): ?self
    {
        $code = substr($phone, 0, 3);

        foreach (self::cases() as $network) {
            if (in_array($code, $network->codes(), true)) {
                return $network;
            }
        }

        return null;
    }
}