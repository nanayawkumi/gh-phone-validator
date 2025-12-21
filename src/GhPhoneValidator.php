<?php

namespace Nanayawkumi\GhPhoneValidator;

class GhPhoneValidator
{
    public static function normalize(string $phone): ?string
    {
        $phone = trim($phone);

        // remove spaces, dashes, brackets
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        if (str_starts_with($phone, '+233')) {
            $phone = '0' . substr($phone, 4);
        }

        if (str_starts_with($phone, '233')) {
            $phone = '0' . substr($phone, 3);
        }

        if (strlen($phone) === 9) {
            $phone = '0' . $phone;
        }

        return strlen($phone) === 10 && ctype_digit($phone)
            ? $phone
            : null;
    }


    public static function network(string $phone): array
    {
        $normalized = self::normalize($phone);

        if (!$normalized) {
            return ['name' => null, 'slug' => null];
        }

        $code = substr($normalized, 0, 3);

        foreach (config('gh-phone-validator.networks') as $slug => $network) {
            if (in_array($code, $network['codes'], true)) {
                return [
                    'name' => $network['name'],
                    'slug' => $slug,
                ];
            }
        }

        return ['name' => null, 'slug' => null];
    }
}