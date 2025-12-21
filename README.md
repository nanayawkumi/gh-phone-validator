# Ghana Phone Validator

A Laravel validation rule and helper package for validating and normalizing Ghana phone numbers.

## Installation

Install the package via Composer:

```bash
composer require nanayawkumi/gh-phone-validator
```

The package will automatically register its service provider if you're using Laravel's package auto-discovery.

## Requirements

- PHP ^8.2
- Laravel ^10.0|^11.0|^12.0

## Usage

### Validation Rule

You can use the `gh_phone` validation rule in your form requests or controllers:

```php
use Illuminate\Support\Facades\Validator;

$validator = Validator::make($request->all(), [
    'phone' => 'required|gh_phone',
]);
```

Or use the rule class directly:

```php
use Nanayawkumi\GhPhoneValidator\Rules\GhPhone;

$validator = Validator::make($request->all(), [
    'phone' => ['required', new GhPhone()],
]);
```

### Normalize Phone Numbers

The `normalize()` method converts various phone number formats to a standard 10-digit format (starting with 0):

```php
use Nanayawkumi\GhPhoneValidator\GhPhoneValidator;

GhPhoneValidator::normalize('241234567');        // Returns: '0241234567'
GhPhoneValidator::normalize('+233241234567');    // Returns: '0241234567'
GhPhoneValidator::normalize('233241234567');     // Returns: '0241234567'
GhPhoneValidator::normalize('024-123-4567');     // Returns: '0241234567'
GhPhoneValidator::normalize('024 123 4567');     // Returns: '0241234567'
GhPhoneValidator::normalize('invalid');          // Returns: null
```

### Detect Network

The `network()` method identifies the network provider for a given phone number and returns a `Network` enum:

```php
use Nanayawkumi\GhPhoneValidator\GhPhoneValidator;
use Nanayawkumi\GhPhoneValidator\Enums\Network;

$network = GhPhoneValidator::network('0241234567');
// Returns: Network::MTN

$network = GhPhoneValidator::network('0201234567');
// Returns: Network::TELECEL

$network = GhPhoneValidator::network('0261234567');
// Returns: Network::AIRTELTIGO

$network = GhPhoneValidator::network('invalid');
// Returns: null
```

For backward compatibility, you can also use `networkInfo()` to get the array format:

```php
$network = GhPhoneValidator::networkInfo('0241234567');
// Returns: ['name' => 'MTN', 'slug' => 'mtn']
```

### Validate Phone Numbers

The `validate()` method checks if a phone number is valid (10 digits and starts with a valid network prefix):

```php
use Nanayawkumi\GhPhoneValidator\GhPhoneValidator;

GhPhoneValidator::validate('0241234567');  // Returns: true
GhPhoneValidator::validate('0201234567');  // Returns: true
GhPhoneValidator::validate('9991234567');  // Returns: false (invalid prefix)
GhPhoneValidator::validate('12345');       // Returns: false (invalid length)
```

### Format Phone Numbers

The package provides several formatting methods to display phone numbers in different formats:

#### Raw Local Format

The `formatRaw()` method returns the phone number in raw local format (10 digits starting with 0):

```php
use Nanayawkumi\GhPhoneValidator\GhPhoneValidator;

GhPhoneValidator::formatRaw('+233241234567');  // Returns: '0241234567'
GhPhoneValidator::formatRaw('024 123 4567');   // Returns: '0241234567'
GhPhoneValidator::formatRaw('invalid');        // Returns: null
```

#### National Format

The `formatNational()` method formats the phone number with spaces for readability:

```php
use Nanayawkumi\GhPhoneValidator\GhPhoneValidator;

GhPhoneValidator::formatNational('0241234567');     // Returns: '024 123 4567'
GhPhoneValidator::formatNational('+233241234567');  // Returns: '024 123 4567'
GhPhoneValidator::formatNational('invalid');         // Returns: null
```

#### International Format

The `formatInternational()` method formats the phone number in international readable format:

```php
use Nanayawkumi\GhPhoneValidator\GhPhoneValidator;

GhPhoneValidator::formatInternational('0241234567');     // Returns: '+233 24 123 4567'
GhPhoneValidator::formatInternational('+233241234567');  // Returns: '+233 24 123 4567'
GhPhoneValidator::formatInternational('invalid');         // Returns: null
```

#### E.164 Format

The `formatE164()` method formats the phone number in E.164 international format:

```php
use Nanayawkumi\GhPhoneValidator\GhPhoneValidator;

GhPhoneValidator::formatE164('0241234567');     // Returns: '+233241234567'
GhPhoneValidator::formatE164('024 123 4567');   // Returns: '+233241234567'
GhPhoneValidator::formatE164('invalid');        // Returns: null
```

### Common Use Cases

#### UI Display

Format phone numbers for display in your user interface:

```blade
{{ GhanaPhone::formatNational($user->phone) }}
```

#### SMS / Telco APIs

Format phone numbers in E.164 format for SMS gateways and telco APIs:

```php
$msisdn = GhanaPhone::formatE164($phone);
```

## Eloquent Cast

This package provides an Eloquent cast for Ghana phone numbers.

### Basic Usage

```php
use SamuelKumi\GhanaPhone\Casts\GhanaPhoneCast;

class User extends Model
{
    protected $casts = [
        'phone' => GhanaPhoneCast::class,
    ];
}
```

### Behavior

- **Accepts messy input when saving**: The cast automatically normalizes various phone number formats (with spaces, dashes, international format, etc.) when saving to the database.

- **Stores numbers in E.164 format**: Phone numbers are stored in the database using the E.164 international format (e.g., `+233241234567`).

- **Returns a value object with formatting helpers**: When retrieving the phone number, you get a `PhoneNumber` value object with convenient formatting methods:

```php
$user->phone->national();        // Returns: '024 123 4567'
$user->phone->international();   // Returns: '+233 24 123 4567'
$user->phone->e164();            // Returns: '+233241234567'
$user->phone->network();          // Returns: ['name' => 'MTN', 'slug' => 'mtn']
$user->phone->raw();              // Returns: '+233241234567'
```

**Example:**

```php
// Saving with messy input
$user = new User();
$user->phone = '024 123 4567';  // Accepts various formats
$user->save();                   // Stored as '+233241234567'

// Retrieving and formatting
$user = User::find(1);
echo $user->phone->national();      // Output: '024 123 4567'
echo $user->phone->international(); // Output: '+233 24 123 4567'
```

## Cast Storage Customization

You can control how phone numbers are stored in the database.

### Store as E.164 (default)

```php
protected $casts = [
    'phone' => GhanaPhoneCast::class,
];
```

### Store as Raw Local Format

```php
protected $casts = [
    'phone' => GhanaPhoneCast::class . ':raw',
];
```

### Notes

- **All formats are normalized before storage**: Regardless of the input format (with spaces, dashes, international format, etc.), the phone number is normalized before being stored.

- **Retrieval always returns a value object**: When retrieving from the database, you always get a `PhoneNumber` value object with formatting helpers, regardless of how it was stored.

- **Strict mode is respected**: The cast respects the strict mode configuration when validating phone numbers before storage.

**Example:**

```php
// Store as E.164 (default)
$user = new User();
$user->phone = '024 123 4567';
$user->save();  // Stored as '+233241234567'

// Store as raw local format
class User extends Model
{
    protected $casts = [
        'phone' => GhanaPhoneCast::class . ':raw',
    ];
}

$user = new User();
$user->phone = '+233241234567';
$user->save();  // Stored as '0241234567'
```

## Network Enum

The package provides a strongly-typed `Network` enum for working with network providers.

### Using the Network Enum

```php
use SamuelKumi\GhanaPhone\Enums\Network;

$network = GhanaPhone::network('0241234567');

if ($network === Network::MTN) {
    // Do something
}

$network->label(); // MTN
$network->slug();  // mtn
```

### From Eloquent Cast

When using the Eloquent cast, the `network()` method returns a `Network` enum:

```php
$user->phone->network(); // Network enum (Network::MTN, Network::TELECEL, etc.)

// Use it in conditionals
if ($user->phone->network() === Network::MTN) {
    // User is on MTN network
}

// Get network label
echo $user->phone->network()?->label(); // MTN

// Get network slug
echo $user->phone->network()?->slug();  // mtn
```

### Available Methods

The `Network` enum provides the following methods:

- `label()` - Returns the human-readable network name (e.g., "MTN", "Telecel")
- `slug()` - Returns the network slug (e.g., "mtn", "telecel")
- `codes()` - Returns an array of network prefix codes
- `fromPhone(string $phone)` - Static method to get network from a phone number

**Example:**

```php
use Nanayawkumi\GhPhoneValidator\Enums\Network;

$network = Network::fromPhone('0241234567');

if ($network) {
    echo $network->label();        // MTN
    echo $network->slug();        // mtn
    print_r($network->codes());    // ['024', '054', '055', ...]
}
```

## Supported Networks

The package recognizes the following network providers:

- **MTN**: 024, 054, 055, 059, 025, 053, 023
- **Telecel**: 020, 050
- **AirtelTigo**: 026, 056, 027, 057

## Configuration

Publish the configuration file to customize network settings:

```bash
php artisan vendor:publish --tag=gh-phone-validator-config
```

This will create a `config/gh-phone-validator.php` file where you can modify network codes and names.

### Strict Mode

Strict mode controls whether the package accepts phone numbers with unknown network prefixes:

- **Enabled (`strict => true`)**: Only accepts phone numbers with prefixes that match known network codes. Numbers with unknown prefixes will return `null` when normalized.
- **Disabled (`strict => false`)**: Accepts any valid 10-digit Ghana phone number starting with 0, even if the prefix isn't in the known networks list.

```php
// In config/gh-phone-validator.php
'strict' => true,  // Reject unknown prefixes
'strict' => false, // Accept any valid 10-digit number (default)
```

**Example:**

```php
// With strict mode enabled
config()->set('gh-phone-validator.strict', true);
GhPhoneValidator::normalize('0291234567');  // Returns: null (unknown prefix)

// With strict mode disabled
config()->set('gh-phone-validator.strict', false);
GhPhoneValidator::normalize('0291234567');  // Returns: '0291234567' (accepted)
```

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## Author

Samuel Kumi-Buabeng
