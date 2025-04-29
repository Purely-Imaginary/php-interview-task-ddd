<?php

namespace Lendable\Interview\Infrastructure\Persistence;

use Lendable\Interview\Domain\Model\Fee\Breakpoint;
use Lendable\Interview\Domain\Model\Fee\FeeStructure;
use Lendable\Interview\Domain\Model\Loan\Money;
use Lendable\Interview\Domain\Model\Loan\Term;
use Lendable\Interview\Domain\Repository\FeeStructureRepository;

final class InMemoryFeeStructureRepository implements FeeStructureRepository
{
    private array $feeStructures = [
        // Term => [ [amount, fee], [amount, fee], ... ]
        Term::MONTHS_12->value => [
            [ 1_000_00 ,  50_00],
            [ 2_000_00 ,  90_00],
            [ 3_000_00 ,  90_00],
            [ 4_000_00 , 115_00],
            [ 5_000_00 , 100_00],
            [ 6_000_00 , 120_00],
            [ 7_000_00 , 140_00],
            [ 8_000_00 , 160_00],
            [ 9_000_00 , 180_00],
            [10_000_00 , 200_00],
            [11_000_00 , 220_00],
            [12_000_00 , 240_00],
            [13_000_00 , 260_00],
            [14_000_00 , 280_00],
            [15_000_00 , 300_00],
            [16_000_00 , 320_00],
            [17_000_00 , 340_00],
            [18_000_00 , 360_00],
            [19_000_00 , 380_00],
            [20_000_00 , 400_00],
        ],
        Term::MONTHS_24->value => [
            [ 1_000_00 ,  70_00],
            [ 2_000_00 , 100_00],
            [ 3_000_00 , 120_00],
            [ 4_000_00 , 160_00],
            [ 5_000_00 , 200_00],
            [ 6_000_00 , 240_00],
            [ 7_000_00 , 280_00],
            [ 8_000_00 , 320_00],
            [ 9_000_00 , 360_00],
            [10_000_00 , 400_00],
            [11_000_00 , 440_00],
            [12_000_00 , 480_00],
            [13_000_00 , 520_00],
            [14_000_00 , 560_00],
            [15_000_00 , 600_00],
            [16_000_00 , 640_00],
            [17_000_00 , 680_00],
            [18_000_00 , 720_00],
            [19_000_00 , 760_00],
            [20_000_00 , 800_00],
        ],
    ];

    private array $structures; // simple caching for findForTerm()

    public function findForTerm(Term $term): ?FeeStructure
    {
        if (isset($this->structures[$term->value])) {
            return $this->structures[$term->value];
        }

        $rawData = $this->feeStructures[$term->value] ?? null;
        if ($rawData === null) {
            return null;
        }

        $breakpoints = [];
        foreach ($rawData as $breakpoint) {
            $breakpoints[] = new Breakpoint(Money::fromMinor($breakpoint[0]), Money::fromMinor($breakpoint[1]));
        }

        // SUPER IMPORTANT: sort the breakpoints by amount so that we can interpolate the fee to be 10000% sure
        usort($breakpoints, function(Breakpoint $a, Breakpoint $b): int {
            return $a->amount->getMinorAmount() <=> $b->amount->getMinorAmount(); // Spaceship operator for comparison <3
        });

        $feeStructure = new FeeStructure($term, $breakpoints);
        $this->structures[$term->value] = $feeStructure;

        return $feeStructure;
    }

}