<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\TermInterface;

/**
 * Interface CategorizableEntityInterface
 */
interface CategorizableEntityInterface
{
    /**
     * @param Collection|TermInterface[] $terms
     */
    public function setTerms(Collection $terms = null);

    public function addTerm(TermInterface $term);

    public function removeTerm(TermInterface $term);

    public function getTerms(): Collection;
}
