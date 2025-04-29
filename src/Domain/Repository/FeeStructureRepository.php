<?php

namespace Lendable\Interview\Domain\Repository;

use Lendable\Interview\Domain\Model\Fee\FeeStructure;
use Lendable\Interview\Domain\Model\Loan\Term;

interface FeeStructureRepository
{
    public function findForTerm(Term $term): ?FeeStructure;
}