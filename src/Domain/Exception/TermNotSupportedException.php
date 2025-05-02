<?php

declare(strict_types=1);

namespace Lendable\Interview\Domain\Exception;

use Lendable\Interview\Domain\Model\Loan\Term;

/**
 * Exception thrown when an unsupported loan term is provided.
 */
class TermNotSupportedException extends \InvalidArgumentException
{
    public function __construct(int $term)
    {
        $supportedTerms = implode(' and ', array_column(Term::cases(), 'value'));
        parent::__construct(sprintf('Term %d months is not supported. Only %s month terms are available.', $term, $supportedTerms));
    }

}
