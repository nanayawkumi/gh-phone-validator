<?php

namespace Nanayawkumi\GhPhoneValidator\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;
use Nanayawkumi\GhPhoneValidator\GhPhoneValidator;
use Nanayawkumi\GhPhoneValidator\ValueObjects\PhoneNumber;

class GhPhoneCast implements CastsAttributes
{
    protected string $storeAs;

    /**
     * @param string $storeAs e164|raw
     */
    public function __construct(string $storeAs = 'e164')
    {
        $this->storeAs = $storeAs;
    }

    /**
     * Cast the given value when retrieving from database.
     */
    public function get($model, string $key, $value, array $attributes): ?PhoneNumber
    {
        if ($value === null) {
            return null;
        }

        return new PhoneNumber($value);
    }

    /**
     * Prepare the given value for storage.
     */
    public function set($model, string $key, $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = GhPhoneValidator::normalize((string) $value);

        if (!$normalized) {
            throw new InvalidArgumentException(
                "The {$key} must be a valid Ghana phone number."
            );
        }

        return match ($this->storeAs) {
            'raw'  => $normalized,
            'e164' => GhPhoneValidator::formatE164($normalized),
            default => throw new InvalidArgumentException(
                "Invalid GhPhoneCast format [{$this->storeAs}]. Supported: raw, e164."
            ),
        };
    }
}