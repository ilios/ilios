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
    public function setPreferred(bool $preferred);
    public function getPreferred(): bool;

    public function setScopeNote(?string $scopeNote);
    public function getScopeNote(): ?string;

    public function setCasn1Name(?string $casn1Name);
    public function getCasn1Name(): ?string;

    public function setRegistryNumber(?string $registryNumber);
    public function getRegistryNumber(): ?string;

    public function setTerms(Collection $terms);
    public function addTerm(MeshTermInterface $term);
    public function removeTerm(MeshTermInterface $term);
    public function getTerms(): Collection;

    public function setDescriptors(Collection $descriptors);
    public function addDescriptor(MeshDescriptorInterface $descriptor);
    public function removeDescriptor(MeshDescriptorInterface $descriptor);
    public function getDescriptors(): Collection;
}
