# Fono Library - AI Agent Documentation

> **Agent Protocol: Keep This Document Updated**
> All agents are required to update this document with any new, relevant information discovered during their work on the Fono library. This includes changes in phone number handling, new country support, updated validation patterns, or newly established formatting rules.

This document provides comprehensive technical documentation for the Fono library, a PHP phone number and data validation utility used in the Hnutí DUHA IS application. The library handles phone number processing, postal codes, bank accounts, and various Czech/Slovak data formats.

---

## Table of Contents

1. [Library Overview](#1-library-overview)
2. [Core Architecture](#2-core-architecture)
3. [Phone Number Processing](#3-phone-number-processing)
4. [Postal Code Processing](#4-postal-code-processing)
5. [Bank Account Processing](#5-bank-account-processing)
6. [Business Identifiers](#6-business-identifiers)
7. [Country Support](#7-country-support)
8. [Common Patterns](#8-common-patterns)
9. [Troubleshooting](#9-troubleshooting)
10. [Development Guidelines](#10-development-guidelines)
11. [API Reference](#11-api-reference)

---

## 1. Library Overview

### 1.1. Purpose

The Fono library is responsible for:

- **Phone Number Processing**: Czech and Slovak phone number validation and formatting
- **Postal Code Processing**: Czech postal code validation and formatting
- **Bank Account Processing**: Czech bank account number validation
- **Business Identifiers**: ICO and DIC validation for Czech businesses
- **Data Validation**: Comprehensive validation for various data formats
- **Data Formatting**: Consistent formatting for display and storage

### 1.2. Key Features

- **Multi-Country Support**: Czech and Slovak phone numbers
- **Data Validation**: Regex-based validation for various formats
- **Data Sanitization**: Automatic cleaning and normalization
- **Data Formatting**: Consistent formatting for display
- **Extensible Architecture**: Easy to add new countries and formats
- **Type Safety**: Strong typing with validation

### 1.3. Core Components

- **Fono**: Abstract base class for all data types
- **Phone**: Phone number processing for CZ and SK
- **PostalCode**: Czech postal code processing
- **BankAccount**: Czech bank account processing
- **ICO**: Czech business identification number
- **DIC**: Czech VAT identification number

### 1.4. Dependencies

- **Katu Framework**: Base framework for utilities and string processing
- **TString**: String processing and normalization

---

## 2. Core Architecture

### 2.1. Fono Base Class

**Location**: `src/Fono.php`

Abstract base class for all data types:

```php
abstract class Fono
{
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    abstract public function getRegexFilter(): string;
}
```

**Key Features**:

- **Abstract Validation**: Each subclass implements its own regex filter
- **Value Storage**: Protected value storage with getter/setter
- **String Conversion**: Automatic string conversion
- **Validation**: Built-in validation using regex patterns
- **Sanitization**: Automatic data cleaning and normalization

### 2.2. Validation System

#### Validation Process

```php
public function getIsValid(): bool
{
    return (bool)preg_match(static::getRegexFilter(), $this->getSanitized());
}
```

#### Sanitization Process

```php
public function getSanitized(): string
{
    $string = new TString($this->getValue());
    $string = $string->getWithNormalizedSpaces();
    $string = preg_replace("/\s/", "", $string);

    return new static($string);
}
```

### 2.3. Formatting System

#### Basic Formatting

```php
public function getFormatted(): string
{
    $string = (string)$this->getSanitized();
    return $string;
}
```

#### String Conversion

```php
public function __toString()
{
    return $this->getValue();
}
```

---

## 3. Phone Number Processing

### 3.1. Czech Phone Numbers

#### Phone Class

```php
class Phone extends \Fono\Fono
{
    public function getIntlPrefix(): string
    {
        return "420";
    }

    public function getRegexFilter(): string
    {
        return "/^(00|\+){$this->getIntlPrefix()}[0-9]{9}$/";
    }
}
```

#### Phone Number Validation

```php
// Create phone number
$phone = new \Fono\Countries\CZ\Phone("+420 123 456 789");

// Check if valid
$isValid = $phone->getIsValid();

// Get sanitized version
$sanitized = $phone->getSanitized();

// Get formatted version
$formatted = $phone->getFormatted();

// Get plain number
$plain = $phone->getPlain();
```

#### Phone Number Sanitization

```php
public function getSanitized(): string
{
    $string = $this->getValue();

    // Remove all spaces, dots and dashes
    $string = preg_replace("/[\s\.\-]/u", "", $string);

    // Replace + with 00
    $string = preg_replace("/^\+/u", "00", $string);

    if (strlen($string) == 9) {
        $string = "00{$this->getIntlPrefix()}{$string}";
    }

    return new static($string);
}
```

#### Phone Number Formatting

```php
public function getFormatted(): string
{
    $string = (string)$this->getSanitized();

    // Is 0042x and 9 digits
    if (preg_match("/^00{$this->getIntlPrefix()}(?<phone>[0-9]{9})$/", $string, $match)) {
        return implode(" ", [
            "+{$this->getIntlPrefix()}",
            ...str_split($match["phone"], 3),
        ]);
    }

    return $string;
}
```

#### Plain Number Extraction

```php
public function getPlain(): string
{
    $string = (string)$this->getSanitized();

    // Is 00420 and 9 digits
    if (preg_match("/^00{$this->getIntlPrefix()}(?<phone>[0-9]{9})$/", $string, $match)) {
        return $match["phone"];
    }

    return $string;
}
```

### 3.2. Slovak Phone Numbers

#### Slovak Phone Class

```php
class Phone extends \Fono\Countries\CZ\Phone
{
    public function getIntlPrefix(): string
    {
        return "421";
    }
}
```

#### Slovak Phone Usage

```php
// Create Slovak phone number
$phone = new \Fono\Countries\SK\Phone("+421 123 456 789");

// Same methods as Czech phone
$isValid = $phone->getIsValid();
$formatted = $phone->getFormatted();
$plain = $phone->getPlain();
```

### 3.3. Phone Number Examples

#### Input Variations

```php
// Various input formats
$inputs = [
    "+420 123 456 789",
    "00420 123 456 789",
    "123 456 789",
    "123456789",
    "+420-123-456-789",
    "00420.123.456.789"
];

foreach ($inputs as $input) {
    $phone = new \Fono\Countries\CZ\Phone($input);
    echo "Input: {$input}\n";
    echo "Valid: " . ($phone->getIsValid() ? "Yes" : "No") . "\n";
    echo "Formatted: {$phone->getFormatted()}\n";
    echo "Plain: {$phone->getPlain()}\n\n";
}
```

#### Output Examples

```
Input: +420 123 456 789
Valid: Yes
Formatted: +420 123 456 789
Plain: 123456789

Input: 123 456 789
Valid: Yes
Formatted: +420 123 456 789
Plain: 123456789
```

---

## 4. Postal Code Processing

### 4.1. Czech Postal Codes

#### PostalCode Class

```php
class PostalCode extends \Fono\Fono
{
    public function getRegexFilter(): string
    {
        return "/^[0-9]{3}\s?[0-9]{2}$/";
    }
}
```

#### Postal Code Validation

```php
// Create postal code
$postalCode = new \Fono\Countries\CZ\PostalCode("123 45");

// Check if valid
$isValid = $postalCode->getIsValid();

// Get sanitized version
$sanitized = $postalCode->getSanitized();

// Get formatted version
$formatted = $postalCode->getFormatted();

// Get plain number
$plain = $postalCode->getPlain();
```

#### Postal Code Formatting

```php
public function getFormatted(): string
{
    return implode(" ", [
        substr($this->getSanitized(), 0, 3),
        substr($this->getSanitized(), 3, 2),
    ]);
}
```

#### Plain Number Extraction

```php
public function getPlain(): string
{
    return (int)preg_replace("/\s/", "", $this->getSanitized());
}
```

### 4.2. Postal Code Examples

#### Input Variations

```php
// Various input formats
$inputs = [
    "123 45",
    "12345",
    "123-45",
    " 123 45 ",
    "123.45"
];

foreach ($inputs as $input) {
    $postalCode = new \Fono\Countries\CZ\PostalCode($input);
    echo "Input: {$input}\n";
    echo "Valid: " . ($postalCode->getIsValid() ? "Yes" : "No") . "\n";
    echo "Formatted: {$postalCode->getFormatted()}\n";
    echo "Plain: {$postalCode->getPlain()}\n\n";
}
```

#### Output Examples

```
Input: 123 45
Valid: Yes
Formatted: 123 45
Plain: 12345

Input: 12345
Valid: Yes
Formatted: 123 45
Plain: 12345
```

---

## 5. Bank Account Processing

### 5.1. Czech Bank Accounts

#### BankAccount Class

```php
class BankAccount extends \Fono\Fono
{
    public function getRegexFilter(): string
    {
        return "/^([0-9]{0,6})?\-?([0-9]{1,10})\/([0-9]{4})$/";
    }
}
```

#### Bank Account Validation

```php
// Create bank account
$bankAccount = new \Fono\Countries\CZ\BankAccount("123456-7890123456/0100");

// Check if valid
$isValid = $bankAccount->getIsValid();

// Get sanitized version
$sanitized = $bankAccount->getSanitized();

// Get formatted version
$formatted = $bankAccount->getFormatted();
```

### 5.2. Bank Account Examples

#### Input Variations

```php
// Various input formats
$inputs = [
    "123456-7890123456/0100",
    "7890123456/0100",
    "1234567890123456/0100",
    "123456-7890123456/0100",
    " 123456-7890123456/0100 "
];

foreach ($inputs as $input) {
    $bankAccount = new \Fono\Countries\CZ\BankAccount($input);
    echo "Input: {$input}\n";
    echo "Valid: " . ($bankAccount->getIsValid() ? "Yes" : "No") . "\n";
    echo "Formatted: {$bankAccount->getFormatted()}\n\n";
}
```

---

## 6. Business Identifiers

### 6.1. ICO (Czech Business ID)

#### ICO Class

```php
class ICO extends \Fono\Fono
{
    public function getRegexFilter(): string
    {
        return "/^([0-9]{8})$/";
    }
}
```

#### ICO Validation

```php
// Create ICO
$ico = new \Fono\Countries\CZ\ICO("12345678");

// Check if valid
$isValid = $ico->getIsValid();

// Get sanitized version
$sanitized = $ico->getSanitized();

// Get formatted version
$formatted = $ico->getFormatted();
```

### 6.2. DIC (Czech VAT ID)

#### DIC Class

```php
class DIC extends \Fono\Fono
{
    public function getRegexFilter(): string
    {
        return "/^CZ([0-9]{8,10})$/";
    }
}
```

#### DIC Validation

```php
// Create DIC
$dic = new \Fono\Countries\CZ\DIC("CZ12345678");

// Check if valid
$isValid = $dic->getIsValid();

// Get sanitized version
$sanitized = $dic->getSanitized();

// Get formatted version
$formatted = $dic->getFormatted();
```

### 6.3. Business Identifier Examples

#### ICO Examples

```php
// ICO input variations
$icoInputs = [
    "12345678",
    "1234567",  // Invalid - too short
    "123456789", // Invalid - too long
    " 12345678 ",
    "1234-5678" // Invalid - contains dash
];

foreach ($icoInputs as $input) {
    $ico = new \Fono\Countries\CZ\ICO($input);
    echo "Input: {$input}\n";
    echo "Valid: " . ($ico->getIsValid() ? "Yes" : "No") . "\n";
    echo "Formatted: {$ico->getFormatted()}\n\n";
}
```

#### DIC Examples

```php
// DIC input variations
$dicInputs = [
    "CZ12345678",
    "CZ123456789",
    "CZ1234567890",
    "12345678",  // Invalid - missing CZ prefix
    "CZ1234567"  // Invalid - too short
];

foreach ($dicInputs as $input) {
    $dic = new \Fono\Countries\CZ\DIC($input);
    echo "Input: {$input}\n";
    echo "Valid: " . ($dic->getIsValid() ? "Yes" : "No") . "\n";
    echo "Formatted: {$dic->getFormatted()}\n\n";
}
```

---

## 7. Country Support

### 7.1. Czech Republic (CZ)

#### Supported Data Types

- **Phone**: Czech phone numbers (+420)
- **PostalCode**: Czech postal codes (123 45)
- **BankAccount**: Czech bank accounts (123456-7890123456/0100)
- **ICO**: Czech business ID (8 digits)
- **DIC**: Czech VAT ID (CZ + 8-10 digits)

#### Usage Examples

```php
// Czech phone number
$phone = new \Fono\Countries\CZ\Phone("+420 123 456 789");

// Czech postal code
$postalCode = new \Fono\Countries\CZ\PostalCode("123 45");

// Czech bank account
$bankAccount = new \Fono\Countries\CZ\BankAccount("123456-7890123456/0100");

// Czech business ID
$ico = new \Fono\Countries\CZ\ICO("12345678");

// Czech VAT ID
$dic = new \Fono\Countries\CZ\DIC("CZ12345678");
```

### 7.2. Slovakia (SK)

#### Supported Data Types

- **Phone**: Slovak phone numbers (+421)

#### Usage Examples

```php
// Slovak phone number
$phone = new \Fono\Countries\SK\Phone("+421 123 456 789");
```

### 7.3. Extending Country Support

#### Adding New Countries

```php
// Create new country phone class
class Phone extends \Fono\Countries\CZ\Phone
{
    public function getIntlPrefix(): string
    {
        return "43"; // Austria
    }
}
```

#### Adding New Data Types

```php
// Create new data type
class TaxID extends \Fono\Fono
{
    public function getRegexFilter(): string
    {
        return "/^[0-9]{9}$/";
    }

    public function getFormatted(): string
    {
        return $this->getSanitized();
    }
}
```

---

## 8. Common Patterns

### 8.1. Basic Validation

```php
// Validate any Fono type
function validateFono(Fono $fono): bool
{
    return $fono->getIsValid();
}

// Usage
$phone = new \Fono\Countries\CZ\Phone("+420 123 456 789");
$isValid = validateFono($phone);
```

### 8.2. Data Processing Pipeline

```php
// Process data through pipeline
function processData(Fono $fono): array
{
    return [
        'original' => $fono->getValue(),
        'sanitized' => $fono->getSanitized(),
        'formatted' => $fono->getFormatted(),
        'valid' => $fono->getIsValid()
    ];
}

// Usage
$phone = new \Fono\Countries\CZ\Phone("+420 123 456 789");
$result = processData($phone);
```

### 8.3. Batch Processing

```php
// Process multiple phone numbers
$phoneNumbers = [
    "+420 123 456 789",
    "+420 987 654 321",
    "+420 555 666 777"
];

$results = [];
foreach ($phoneNumbers as $number) {
    $phone = new \Fono\Countries\CZ\Phone($number);
    $results[] = [
        'input' => $number,
        'valid' => $phone->getIsValid(),
        'formatted' => $phone->getFormatted(),
        'plain' => $phone->getPlain()
    ];
}
```

### 8.4. Data Normalization

```php
// Normalize data for storage
function normalizeForStorage(Fono $fono): string
{
    if ($fono->getIsValid()) {
        return $fono->getSanitized();
    }

    return $fono->getValue();
}

// Usage
$phone = new \Fono\Countries\CZ\Phone("+420 123 456 789");
$normalized = normalizeForStorage($phone);
```

### 8.5. Display Formatting

```php
// Format data for display
function formatForDisplay(Fono $fono): string
{
    if ($fono->getIsValid()) {
        return $fono->getFormatted();
    }

    return $fono->getValue();
}

// Usage
$phone = new \Fono\Countries\CZ\Phone("+420 123 456 789");
$display = formatForDisplay($phone);
```

---

## 9. Troubleshooting

### 9.1. Common Issues

#### Validation Failures

- Check input format against regex patterns
- Verify country-specific requirements
- Handle special characters properly
- Check for required prefixes

#### Formatting Issues

- Ensure proper sanitization before formatting
- Handle edge cases in formatting logic
- Check for required data length
- Verify formatting patterns

#### Country-Specific Issues

- Verify international prefixes
- Check country-specific validation rules
- Handle different number formats
- Ensure proper country detection

### 9.2. Debugging

#### Validation Debugging

```php
// Debug validation
$phone = new \Fono\Countries\CZ\Phone("+420 123 456 789");
echo "Input: " . $phone->getValue() . "\n";
echo "Sanitized: " . $phone->getSanitized() . "\n";
echo "Valid: " . ($phone->getIsValid() ? "Yes" : "No") . "\n";
echo "Formatted: " . $phone->getFormatted() . "\n";
```

#### Regex Pattern Debugging

```php
// Debug regex patterns
$phone = new \Fono\Countries\CZ\Phone("+420 123 456 789");
$pattern = $phone->getRegexFilter();
$sanitized = $phone->getSanitized();
$matches = preg_match($pattern, $sanitized);

echo "Pattern: {$pattern}\n";
echo "Sanitized: {$sanitized}\n";
echo "Matches: " . ($matches ? "Yes" : "No") . "\n";
```

#### Data Processing Debugging

```php
// Debug data processing
$phone = new \Fono\Countries\CZ\Phone("+420 123 456 789");
$steps = [
    'original' => $phone->getValue(),
    'sanitized' => $phone->getSanitized(),
    'formatted' => $phone->getFormatted(),
    'plain' => $phone->getPlain()
];

foreach ($steps as $step => $value) {
    echo "{$step}: {$value}\n";
}
```

---

## 10. Development Guidelines

### 10.1. Library Development

**Requirements**:

- Implement abstract `getRegexFilter()` method
- Handle data sanitization properly
- Implement country-specific logic
- Support both validation and formatting

### 10.2. Data Validation

**Best Practices**:

- Use comprehensive regex patterns
- Handle edge cases properly
- Implement proper sanitization
- Support multiple input formats

### 10.3. Data Formatting

**Guidelines**:

- Implement consistent formatting
- Handle display requirements
- Support storage formats
- Provide plain text extraction

### 10.4. Country Support

**Extension Guidelines**:

- Follow existing patterns
- Implement country-specific logic
- Handle international prefixes
- Support local formatting rules

---

## 11. API Reference

### 11.1. Fono Base Class

```php
abstract class Fono
{
    // Constructor
    public function __construct($value);

    // Abstract methods
    abstract public function getRegexFilter(): string;

    // Properties
    public function getValue(): string;
    public function setValue(string $value): Fono;

    // Validation
    public function getIsValid(): bool;

    // Processing
    public function getSanitized(): string;
    public function getFormatted(): string;

    // String conversion
    public function __toString();
}
```

### 11.2. Phone Classes

```php
class Phone extends Fono
{
    // Abstract methods
    abstract public function getIntlPrefix(): string;

    // Overridden methods
    public function getSanitized(): string;
    public function getFormatted(): string;
    public function getPlain(): string;
}

// Czech implementation
class \Fono\Countries\CZ\Phone extends Phone
{
    public function getIntlPrefix(): string; // Returns "420"
}

// Slovak implementation
class \Fono\Countries\SK\Phone extends Phone
{
    public function getIntlPrefix(): string; // Returns "421"
}
```

### 11.3. Postal Code Class

```php
class \Fono\Countries\CZ\PostalCode extends Fono
{
    // Overridden methods
    public function getPlain(): string;
    public function getFormatted(): string;
}
```

### 11.4. Bank Account Class

```php
class \Fono\Countries\CZ\BankAccount extends Fono
{
    // Uses default implementation
}
```

### 11.5. Business Identifier Classes

```php
class \Fono\Countries\CZ\ICO extends Fono
{
    // Uses default implementation
}

class \Fono\Countries\CZ\DIC extends Fono
{
    // Uses default implementation
}
```

### 11.6. Usage Examples

#### Basic Usage

```php
// Create and validate phone number
$phone = new \Fono\Countries\CZ\Phone("+420 123 456 789");
$isValid = $phone->getIsValid();
$formatted = $phone->getFormatted();
```

#### Advanced Usage

```php
// Process multiple data types
$data = [
    'phone' => new \Fono\Countries\CZ\Phone("+420 123 456 789"),
    'postal' => new \Fono\Countries\CZ\PostalCode("123 45"),
    'ico' => new \Fono\Countries\CZ\ICO("12345678")
];

foreach ($data as $type => $fono) {
    echo "{$type}: {$fono->getFormatted()} (Valid: " . ($fono->getIsValid() ? "Yes" : "No") . ")\n";
}
```

#### Country-Specific Usage

```php
// Czech phone number
$czPhone = new \Fono\Countries\CZ\Phone("+420 123 456 789");

// Slovak phone number
$skPhone = new \Fono\Countries\SK\Phone("+421 123 456 789");

// Both support same methods
$czFormatted = $czPhone->getFormatted();
$skFormatted = $skPhone->getFormatted();
```

This comprehensive documentation covers the Fono library, providing AI agents with detailed information about phone number processing, postal codes, bank accounts, business identifiers, and multi-country support.
