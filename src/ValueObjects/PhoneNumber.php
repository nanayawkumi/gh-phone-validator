<?php

namespace Nanayawkumi\GhPhoneValidator\ValueObjects;

use JsonSerializable;
use Nanayawkumi\GhPhoneValidator\Enums\Network;
use Nanayawkumi\GhPhoneValidator\GhPhoneValidator;

final class PhoneNumber implements JsonSerializable, \Stringable
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

    /**
     * Specify data which should be serialized to JSON.
     * This ensures the phone number is serialized as a string, not an object.
     */
    public function jsonSerialize(): string
    {
        return $this->raw;
    }

    /**
     * Serialize the object for storage (cache, sessions, etc.).
     * This ensures the phone number is properly serialized when stored.
     */
    public function __serialize(): array
    {
        return ['raw' => $this->raw];
    }

    /**
     * Unserialize the object from storage.
     * This ensures the phone number is properly restored when retrieved from cache/sessions.
     */
    public function __unserialize(array $data): void
    {
        $this->raw = $data['raw'];
    }
}
