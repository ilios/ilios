<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\TermInterface;
use Doctrine\Common\Collections\Collection;

interface CategorizableEntityInterface
{
    public function setTerms(?Collection $terms = null): void;
    public function addTerm(TermInterface $term): void;
    public function removeTerm(TermInterface $term): void;
    public function getTerms(): Collection;
}
