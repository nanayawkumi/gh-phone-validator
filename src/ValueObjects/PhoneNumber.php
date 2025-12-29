<?php

namespace Nanayawkumi\GhPhoneValidator\ValueObjects;

use Nanayawkumi\GhPhoneValidator\Enums\Network;
use Nanayawkumi\GhPhoneValidator\GhPhoneValidator;

final class PhoneNumber
{
    public function __construct(
        protected string $raw
    ) {}

    public function raw(): string
    {
        return $this->raw;
    }

    public function national(): ?string
    {
        return GhPhoneValidator::formatNational($this->raw);
    }

    public function international(): ?string
    {
        return GhPhoneValidator::formatInternational($this->raw);
    }

    public function e164(): ?string
    {
        return GhPhoneValidator::formatE164($this->raw);
    }

    public function network(): ?Network
    {
        return GhPhoneValidator::network($this->raw);
    }

    public function networkLabel(): ?string
    {
        return $this->network()?->label();
    }

    public function networkSlug(): ?string
    {
        return $this->network()?->slug();
    }

    public function __toString(): string
    {
        return $this->raw;
    }

    /**
     * Allow the object to be called as a method.
     * This enables syntax like: $user->phone()->e164()
     */
    public function __invoke(): self
    {
        return $this;
    }
}
