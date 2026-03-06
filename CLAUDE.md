# CLAUDE.md - Paymentwall PHP SDK

## Project Overview

Official Paymentwall PHP library (v2.2.4) for integrating digital payment processing APIs. Supports Virtual Currency, Digital Goods, Cart API, Brick (credit card processing), and Mobiamo (mobile payments).

**License:** MIT | **PHP:** >= 5.2 (composer.json) | **Extensions:** curl, json

### PHP Version Compatibility

- **composer.json declares** `>=5.2`, but the codebase received PHP 8.2 compatibility fixes (explicit property declarations, `array()` to `[]` syntax, CIDR method refactoring)
- **Library code** works on PHP 5.2 through 8.4+ (tested)
- **Test suite (Behat 2.4)** is broken on PHP 8.x — Symfony Console signature incompatibility (`getSynopsis()` method). Tests require PHP 7.x or earlier to run, or Behat needs upgrading
- **Current stable PHP** is 8.5 (as of 2026). PHP 8.0 and earlier are EOL
- The `>=5.2` minimum in composer.json is outdated — in practice, users run this on PHP 7.4–8.x

## Repository Structure

```
lib/Paymentwall/           # Core SDK library (~1,500 LOC)
  Instance.php             # Abstract base class for all classes
  Config.php               # Singleton configuration manager
  Base.php                 # Deprecated compatibility layer (do not modify)
  Widget.php               # Payment widget URL/HTML generation
  Pingback.php             # Webhook/callback validation
  Product.php              # Product data model
  Card.php                 # Credit card model
  Charge.php               # Brick credit card charges
  Subscription.php         # Recurring billing
  OneTimeToken.php         # Card tokenization
  Mobiamo.php              # Mobile/SMS payments
  HttpAction.php           # cURL HTTP handler (TLS 1.2)
  ApiObject.php            # Abstract base for API operations
  GenerericApiObject.php   # Generic API object (note: typo is intentional, do not rename)
  Signature/
    Abstract.php           # Base signature calculator
    Widget.php             # Widget signature (v1/v2/v3)
    Pingback.php           # Pingback signature (v1/v2/v3)
  Response/
    Factory.php            # Response type factory
    Abstract.php           # Base response
    Interface.php          # Response interface
    Success.php / Error.php
lib/paymentwall.php        # Entry point - manual require_once loader

features/                  # Behat BDD tests
  widget.feature           # Widget signature tests
  pingback.feature         # Pingback validation tests
  charge.feature           # Brick charge tests
  bootstrap/
    FeatureContext.php      # Main test context (loads sub-contexts)
    WidgetContext.php       # Widget test steps
    PingbackContext.php     # Pingback test steps
    ChargeContext.php       # Charge test steps
```

## Class Hierarchy

```
Paymentwall_Instance (abstract base)
├── Paymentwall_Config         (singleton)
├── Paymentwall_Base           (deprecated alias)
├── Paymentwall_Widget
├── Paymentwall_Pingback
├── Paymentwall_HttpAction
├── Paymentwall_Signature_Abstract
│   ├── Paymentwall_Signature_Widget
│   └── Paymentwall_Signature_Pingback
└── Paymentwall_ApiObject (abstract)
    ├── Paymentwall_Charge
    ├── Paymentwall_Subscription
    ├── Paymentwall_OneTimeToken
    ├── Paymentwall_Mobiamo
    └── Paymentwall_GenerericApiObject
```

## Build & Test

```bash
# Install dependencies and run tests
composer test

# Or run Behat directly
vendor/behat/behat/bin/behat
```

Tests use **Behat 2.4** (BDD). Feature files are in `features/`, step definitions in `features/bootstrap/`. Tests validate signature generation, pingback verification, and charge operations using hardcoded test keys (prefix `t_`).

> **Known issue:** Behat 2.4 crashes on PHP 8.x with a fatal error in `InputDefinition::getSynopsis()`. To run tests, either use PHP 7.x or upgrade Behat to 3.x+ (which would require rewriting test contexts).

## Code Conventions

- **Class naming:** `Paymentwall_ComponentName` (underscore-separated, PascalCase components)
- **Methods:** camelCase
- **Constants:** UPPER_CASE
- **Protected properties:** `$property` | **Private properties:** `$_property`
- **Patterns used:** Singleton (Config), Factory (Response), Abstract base classes
- **Property access:** Uses `__get()` magic method on API objects
- **Configuration:** Chained setters via `set(['key' => 'value'])` on Config singleton
- **Autoloading:** Classmap via Composer for `lib/Paymentwall/`; manual includes in `lib/paymentwall.php`

## Key Technical Details

- **Signature versions:** v1 (MD5, legacy), v2 (MD5, sorted params), v3 (SHA256, sorted params — preferred)
- **API types:** `API_VC` (1), `API_GOODS` (2), `API_CART` (3) — set via `Paymentwall_Config`
- **Base URL:** `https://api.paymentwall.com/api`
- **Brick endpoints:** `/api/brick/charge`, `/api/brick/subscription`, `/api/brick/token`
- **OneTimeToken production URL:** `https://pwgateway.com/api/token`
- **Pingback IP whitelist:** Hardcoded IPs + CIDR ranges validated in `Pingback.php`
- **Error handling:** Errors accumulate in `$errors` array, retrieved via `getErrorSummary()`

## Important Notes for AI Assistants

- `GenerericApiObject.php` has an intentional typo in the name — do not rename it
- `Paymentwall_Base` is deprecated; use `Paymentwall_Config` for new code
- The library targets PHP 5.2+ compatibility in composer.json — avoid namespaces, traits, typed properties, union types, enums, and other PHP 7+/8+ features
- PHP 8.2 compatibility fixes were already applied (explicit property declarations to avoid dynamic property deprecation warnings, `array()` to `[]` modernization). Further PHP 8.x compatibility work should follow the same pattern
- Test keys use the `t_` prefix to indicate test mode
- When modifying signature logic, test all three versions (v1, v2, v3) across all API types
- `lib/paymentwall.php` manually includes files in dependency order — update it if adding new classes
