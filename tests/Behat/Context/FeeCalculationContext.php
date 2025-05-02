<?php

declare(strict_types=1);

namespace Lendable\Interview\Tests\Behat\Context;

use Behat\Behat\Context\Context;
use PHPUnit\Framework\Assert;

/**
 * Defines application features from the specific context.
 */
class FeeCalculationContext implements Context
{
    private ?string $output = null;
    private ?int $exitCode = null;
    private string $projectRoot;

    public function __construct()
    {
        $this->projectRoot = dirname(__DIR__, 3);
    }

    /**
     * @Given I want to calculate the fee for a loan
     */
    public function iWantToCalculateTheFeeForALoan(): void
    {
        $this->output = null;
        $this->exitCode = null;
    }

    /**
     * @When I run the calculate-fee command with amount :amount and term :term
     */
    public function iRunTheCalculateFeeCommandWithAmountAndTerm(string $amount, string $term): void
    {
        // Construct the command, ensuring paths and arguments are correct
        $command = sprintf(
            '%s %s %s %s',
            escapeshellarg(PHP_BINARY),
            escapeshellarg($this->projectRoot . '/bin/calculate-fee'),
            escapeshellarg($amount),
            escapeshellarg($term)
        );

        $output = [];
        exec($command . ' 2>&1', $output, $this->exitCode);
        $this->output = implode("\n", $output);
    }

    /**
     * @Then the command should succeed
     */
    public function theCommandShouldSucceed(): void
    {
        Assert::assertSame(0, $this->exitCode, "Command failed with exit code {$this->exitCode}. Output:\n{$this->output}");
    }

    /**
     * @Then the command should fail
     */
    public function theCommandShouldFail(): void
    {
        Assert::assertNotSame(0, $this->exitCode, "Command unexpectedly succeeded (exit code 0). Output:\n{$this->output}");
    }

    /**
     * @Then the output should be :expectedOutput
     */
    public function theOutputShouldBe(string $expectedOutput): void
    {
        Assert::assertSame($expectedOutput, trim($this->output ?? ''), "Output did not match expected value.");
    }

    /**
     * @Then the error output should contain :errorMessageSnippet
     */
    public function theErrorOutputShouldContain(string $errorMessageSnippet): void
    {
        Assert::assertNotNull($this->output, "Command produced no output.");
        Assert::assertStringContainsString($errorMessageSnippet, $this->output, "Output did not contain the expected error message snippet.");
    }
}