<?php

declare(strict_types=1);

namespace Lendable\Interview\Functional;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CalculateFeeCommandTest extends TestCase
{
    /**
     * @param array<string> $args
     * @return array<string, int|string>
     */
    private function executeCommand(array $args): array
    {
        $command = sprintf(
            '%s %s %s',
            escapeshellarg(PHP_BINARY),
            escapeshellarg(__DIR__ . '/../../bin/calculate-fee'),
            implode(' ', array_map('escapeshellarg', $args))
        );

        $output = [];
        $exitCode = -1;
        exec($command . ' 2>&1', $output, $exitCode);

        return [
            'output' => implode("\n", $output),
            'exitCode' => $exitCode,
        ];
    }

    #[Test]
    public function testSuccessfulCalculationExample1(): void
    {
        $result = $this->executeCommand(['11500.00', '24']);

        $this->assertSame(0, $result['exitCode']);
        $this->assertSame('460.00', trim($result['output']));
    }

    #[Test]
    public function testSuccessfulCalculationExample2(): void
    {
        $result = $this->executeCommand(['19250.00', '12']);

        $this->assertSame(0, $result['exitCode']);
        $this->assertSame('385.00', trim($result['output']));
    }

    #[Test]
    public function testHandlesAmountTooLow(): void
    {
        $result = $this->executeCommand(['500.00', '12']);

        $this->assertNotSame(0, $result['exitCode']);

        $this->assertStringContainsString('Loan amount', $result['output']);
        $this->assertStringContainsString('not supported', $result['output']);
    }

    #[Test]
    public function testHandlesInvalidTerm(): void
    {
        $result = $this->executeCommand(['1000.00', '13']);

        $this->assertNotSame(0, $result['exitCode']);
        $this->assertStringContainsString('Term 13 months is not supported', $result['output']);
    }
}
