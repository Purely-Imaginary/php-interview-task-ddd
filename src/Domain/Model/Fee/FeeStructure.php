<?php

namespace Lendable\Interview\Domain\Model\Fee;

use Lendable\Interview\Domain\Model\Loan\Money;
use Lendable\Interview\Domain\Model\Loan\Term;

final class FeeStructure
{
    public function __construct(
        private readonly Term $term,
        /** @var Breakpoint[] */
        private readonly array $breakpoints
    ) {
        if (empty($breakpoints)) {
            throw new \InvalidArgumentException('At least one breakpoint must be provided.');
        }

        foreach ($breakpoints as $breakpoint) {
            if (!$breakpoint instanceof Breakpoint) {
                throw new \InvalidArgumentException('All breakpoints must be instances of Breakpoint class.');
            }
        }
    }

    public function getTerm(): Term
    {
        return $this->term;
    }

    /**
     * @return Breakpoint[]
     */
    public function findBoundaryBreakpoints(Money $loanAmount): array
    {
        $lowerBound = $this->breakpoints[0];

        foreach ($this->breakpoints as $breakpoint) {
            if ($loanAmount->equals($breakpoint->amount)) {
                return [
                    'lower' => $breakpoint,
                    'upper' => $breakpoint
                ];
            }
            if ($loanAmount->isLessThan($breakpoint->amount)) {
                return [
                    'lower' => $lowerBound,
                    'upper' => $breakpoint
                ];
            }
            $lowerBound = $breakpoint;
        }

        return [ // this shouldn't happen, but just in case the max loan amount changes
            'lower' => $lowerBound,
            'upper' => $lowerBound
        ];
    }
}