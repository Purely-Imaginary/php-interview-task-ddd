# Loan Fee Calculator - Showcase of Modern PHP Development

A sophisticated command-line application demonstrating advanced PHP development practices through a loan fee calculation system.

## Project Overview

This project showcases my expertise in building robust, maintainable PHP applications using modern software engineering principles. It implements a loan fee calculator that determines appropriate fees based on loan amount and term, following specific business rules.

### Key Technical Highlights

- **Clean Architecture** with clear separation of concerns (Domain, Application, Infrastructure layers)
- **Domain-Driven Design (DDD)** principles for modeling complex business logic
- **SOLID principles** throughout the codebase
- **Design Patterns** including Strategy, Repository, and Factory patterns
- **Comprehensive Testing** with Unit, Integration, Functional, and BDD tests
- **Modern PHP 8.4** features and type safety
- **Dependency Injection** for loose coupling and testability
- **Event-driven architecture** for extensibility

## Business Problem Solved

The application calculates loan fees based on:

- Loan amounts between £1,000 and £20,000
- Loan terms of either 12 or 24 months
- Complex fee structure with breakpoints
- Linear interpolation for values between breakpoints
- Special rounding rules ensuring the sum of loan amount and fee is divisible by £5

## Technical Implementation

### Architecture

The project follows a clean, layered architecture:

- **Domain Layer**: Contains the core business logic, entities, value objects, and domain services
- **Application Layer**: Orchestrates the use cases and application flow
- **Infrastructure Layer**: Provides implementations for interfaces defined in the domain

This architecture ensures the business logic remains isolated from external concerns, making the system more maintainable and testable.

### Code Quality & Best Practices

- **Type Safety**: Strict typing throughout the codebase
- **Immutable Objects**: Value objects with immutability for safer code
- **Interface Segregation**: Well-defined interfaces with single responsibilities
- **Dependency Inversion**: High-level modules depend on abstractions
- **Command-Query Separation**: Clear distinction between commands and queries
- **Automated Code Quality Tools**: PHPStan, PHP-CS-Fixer, and Rector

### Testing Strategy

The project demonstrates a comprehensive testing approach:

- **Unit Tests**: Testing individual components in isolation
- **Integration Tests**: Testing interactions between components
- **Functional Tests**: Testing application functionality as a whole
- **Behavior-Driven Tests**: Using Behat for acceptance testing

## Usage

### Requirements

- PHP 8.4 or higher
- Composer

### Installation

1. Clone the repository:
   ```
   git clone [repository-url]
   ```

2. Install dependencies:
   ```
   composer install
   ```

### Running the Application

```
bin/calculate-fee <amount> <term>
```

Where:
- `<amount>` is the loan amount in GBP (between £1,000 and £20,000)
- `<term>` is the loan term in months (either 12 or 24)

#### Examples

```
bin/calculate-fee 11500.00 24
```
Output: `460.00`

```
bin/calculate-fee 19250.00 12
```
Output: `385.00`

### Development Tools

```
composer run phpunit-test  # Run PHPUnit tests
composer run behat-test    # Run Behat tests
composer run cs-fixer-fix  # Fix code style issues
composer run rector-fix    # Apply automatic code refactoring
composer run phpstan       # Run static analysis
composer run check-all     # Run all checks and tests
```

## Project Structure

```
├── bin/                  # CLI scripts
├── src/                  # Source code
│   ├── Application/      # Application services, handlers
│   ├── Domain/           # Business logic, entities, value objects
│   │   ├── Event/        # Domain events
│   │   ├── Exception/    # Domain-specific exceptions
│   │   ├── Model/        # Domain models and value objects
│   │   ├── Repository/   # Data access interfaces
│   │   └── Service/      # Domain services
│   └── Infrastructure/   # External concerns, implementations
├── tests/                # Test suite
│   ├── Behat/            # BDD tests
│   ├── Functional/       # Functional tests
│   ├── Integration/      # Integration tests
│   └── Unit/             # Unit tests
└── config/               # Configuration files
```

## About the Author

This project demonstrates my commitment to software craftsmanship and modern PHP development practices. I focus on creating maintainable, testable, and robust applications by applying industry best practices and design principles.

My expertise includes:
- Domain-Driven Design and Clean Architecture
- Test-Driven Development
- SOLID principles and design patterns
- Modern PHP development
- Building scalable and maintainable systems

I'm passionate about creating high-quality software that solves real business problems while maintaining technical excellence.
