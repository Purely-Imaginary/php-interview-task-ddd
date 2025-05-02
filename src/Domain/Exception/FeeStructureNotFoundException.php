<?php

declare(strict_types=1);

namespace Lendable\Interview\Domain\Exception;

/**
 * Exception thrown when no fee structure has been found for a given term.
 */
class FeeStructureNotFoundException extends \RuntimeException
{
    public function __construct(int $term)
    {
        parent::__construct(sprintf('No fee structure found for term %d months.', $term));
    }

}
