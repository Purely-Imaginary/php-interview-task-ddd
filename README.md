# Fee Calculator

A command-line tool for calculating loan fees based on loan amount and term.

## Description

This application calculates the appropriate fee for a loan based on a fee structure and a set of rules. The fee calculation is based on:

- The loan amount (between £1,000 and £20,000)
- The loan term (either 12 or 24 months)
- A predefined fee structure with breakpoints
- Linear interpolation for values between breakpoints
- Rounding rules to ensure the sum of the fee and loan amount is divisible by £5

## Requirements

- PHP 8.4 or higher
- Composer

## Installation

1. Clone the repository:
   ```
   git clone [repository-url]
   ```

2. Install dependencies:
   ```
   composer install
   ```

## Usage

Run the calculator with the loan amount and term as arguments:

```
bin/calculate-fee <amount> <term>
```

Where:
- `<amount>` is the loan amount in GBP (between £1,000 and £20,000)
- `<term>` is the loan term in months (either 12 or 24)

### Examples

```
bin/calculate-fee 11500.00 24
```
Output: `460.00`

```
bin/calculate-fee 19250.00 12
```
Output: `385.00`

## Fee Structure

The fee structure is based on breakpoints for different loan amounts and terms. Values between breakpoints are interpolated linearly.

### Term 12 Months
| Amount | Fee |
|--------|-----|
| 1,000  | 50  |
| 2,000  | 90  |
| 3,000  | 90  |
| ...    | ... |
| 20,000 | 400 |

### Term 24 Months
| Amount | Fee |
|--------|-----|
| 1,000  | 70  |
| 2,000  | 100 |
| 3,000  | 120 |
| ...    | ... |
| 20,000 | 800 |

For the complete fee structure, see the [Task Description](taskDescription.md).

## Project Structure

- `bin/` - Contains the CLI script
- `src/` - Source code
  - `Application/` - Application layer (handlers, services)
  - `Domain/` - Domain layer (models, services)
  - `Infrastructure/` - Infrastructure layer
- `tests/` - Test files
  - `Behat/` - Behat tests
  - `Unit/` - PHPUnit tests
  - `Functional/` - Functional tests
  - `Integration/` - Integration tests
- `config/` - Configuration files

## Development

### Running Tests

```
composer run phpunit-test  # Run PHPUnit tests
composer run behat-test    # Run Behat tests
```

### Code Quality Tools

```
composer run cs-fixer-fix  # Fix code style issues
composer run rector-fix    # Apply automatic code refactoring
composer run phpstan       # Run static analysis
composer run check-all     # Run all checks and tests
```

## Rules

- The fee structure does not follow a formula
- Values between breakpoints are interpolated linearly
- The fee is rounded up so that (loan amount + fee) is divisible by £5
- Loan amount must be between £1,000 and £20,000
- Loan term must be either 12 or 24 months