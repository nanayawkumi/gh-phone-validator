<?php

namespace Nanayawkumi\GhPhoneValidator;

use Nanayawkumi\GhPhoneValidator\Enums\Network;

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

        if (config('gh-phone-validator.strict') && !self::isKnownPrefix($phone)) {
            return null;
        }

        if (strlen($phone) !== 10 || !ctype_digit($phone)) {
            return null;
        }

        return $phone;
    }


    /**
     * Raw local format: 0241234567
     */
    public static function formatRaw(string $phone): ?string
    {
        return self::normalize($phone);
    }

    /**
     * National format: 024 123 4567
     */
    public static function formatNational(string $phone): ?string
    {
        $normalized = self::normalize($phone);

        if (!$normalized) {
            return null;
        }

        return substr($normalized, 0, 3) . ' '
            . substr($normalized, 3, 3) . ' '
            . substr($normalized, 6, 4);
    }

    /**
     * International readable format: +233 24 123 4567
     */
    public static function formatInternational(string $phone): ?string
    {
        $normalized = self::normalize($phone);

        if (!$normalized) {
            return null;
        }

        return '+233 '
            . substr($normalized, 1, 2) . ' '
            . substr($normalized, 3, 3) . ' '
            . substr($normalized, 6, 4);
    }

    /**
     * E.164 format: +233241234567
     */
    public static function formatE164(string $phone): ?string
    {
        $normalized = self::normalize($phone);

        if (!$normalized) {
            return null;
        }

        return '+233' . substr($normalized, 1);
    }

    public static function network(string $phone): ?Network
    {
        $normalized = self::normalize($phone);

        if (!$normalized) {
            return null;
        }

        return Network::fromPhone($normalized);
    }

    public static function networkInfo(string $phone): array
    {
        $network = self::network($phone);

        if (!$network) {
            return ['name' => null, 'slug' => null];
        }

        return [
            'name' => $network->label(),
            'slug' => $network->slug(),
        ];
    }


    /**
     * Validate that a phone number is correct.
     * 
     * Checks that the number:
     * - Is 10 digits after normalization
     * - Starts with a valid network prefix
     * 
     * @param string $phone The phone number to validate
     * @return bool True if the phone number is valid, false otherwise
     */
    public static function validate(string $phone): bool
    {
        $normalized = self::normalize($phone);

        if (!$normalized || strlen($normalized) !== 10) {
            return false;
        }

        // Check if the number starts with a valid network prefix
        $code = substr($normalized, 0, 3);
        $validPrefixes = self::getValidNetworkPrefixes();

        return in_array($code, $validPrefixes, true);
    }

    /**
     * Get all valid network prefixes from configuration.
     * 
     * @return array Array of valid 3-digit network prefixes
     */
    protected static function getValidNetworkPrefixes(): array
    {
        $prefixes = [];
        $networks = config('gh-phone-validator.networks', []);

        foreach ($networks as $network) {
            if (isset($network['codes']) && is_array($network['codes'])) {
                $prefixes = array_merge($prefixes, $network['codes']);
            }
        }

        return $prefixes;
    }

    protected static function isKnownPrefix(string $phone): bool
    {
        return Network::fromPhone($phone) !== null;
    }
}