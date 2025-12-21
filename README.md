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

The `network()` method identifies the network provider for a given phone number:

```php
use Nanayawkumi\GhPhoneValidator\GhPhoneValidator;

$network = GhPhoneValidator::network('0241234567');
// Returns: ['name' => 'MTN', 'slug' => 'mtn']

$network = GhPhoneValidator::network('0201234567');
// Returns: ['name' => 'Telecel', 'slug' => 'telecel']

$network = GhPhoneValidator::network('0261234567');
// Returns: ['name' => 'AirtelTigo', 'slug' => 'airteltigo']

$network = GhPhoneValidator::network('invalid');
// Returns: ['name' => null, 'slug' => null]
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

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## Author

Samuel Kumi-Buabeng
