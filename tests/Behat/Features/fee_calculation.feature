# tests/Behat/Features/fee_calculation.feature
Feature: Fee Calculation Command
  As a user of the system
  I want to calculate loan fees via the command line
  So that I know the correct fee for a given loan amount and term

  Scenario Outline: Calculating fee for valid loan amounts and terms
    Given I want to calculate the fee for a loan
    When I run the calculate-fee command with amount "<Amount>" and term "<Term>"
    Then the command should succeed
    And the output should be "<Expected Fee>"

    Examples:
      | Amount    | Term | Expected Fee |
      | 11500.00  | 24   | 460.00       |
      | 19250.00  | 12   | 385.00       |
      | 1000.00   | 12   | 50.00        |
      | 20000.00  | 24   | 800.00       |
      | 1500.00   | 12   | 70.00        |
      | 3500.00   | 12   | 105.00       |

  Scenario Outline: Attempting to calculate fee for invalid inputs
    Given I want to calculate the fee for a loan
    When I run the calculate-fee command with amount "<Amount>" and term "<Term>"
    Then the command should fail
    And the error output should contain "<Error Message Snippet>"

    Examples:
      | Amount    | Term | Error Message Snippet             |
      | 999.99    | 12   | Loan amount .* not supported      |
      | 20000.01  | 24   | Loan amount .* not supported      |
      | 1000.00   | 13   | Term 13 months is not supported   |
      | abc       | 12   | Invalid decimal string            |