<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\CreatedAtEntityInterface;
use App\Traits\IdentifiableStringEntityInterface;
use Doctrine\Common\Collections\Collection;
use App\Traits\NameableEntityInterface;
use App\Traits\TimestampableEntityInterface;

interface MeshConceptInterface extends
    IdentifiableStringEntityInterface,
    NameableEntityInterface,
    TimestampableEntityInterface,
    CreatedAtEntityInterface
{
    public function setPreferred(bool $preferred): void;
    public function getPreferred(): bool;

    public function setScopeNote(?string $scopeNote): void;
    public function getScopeNote(): ?string;

    public function setCasn1Name(?string $casn1Name): void;
    public function getCasn1Name(): ?string;

    public function setTerms(Collection $terms): void;
    public function addTerm(MeshTermInterface $term): void;
    public function removeTerm(MeshTermInterface $term): void;
    public function getTerms(): Collection;

    public function setDescriptors(Collection $descriptors): void;
    public function addDescriptor(MeshDescriptorInterface $descriptor): void;
    public function removeDescriptor(MeshDescriptorInterface $descriptor): void;
    public function getDescriptors(): Collection;
}
