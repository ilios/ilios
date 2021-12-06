<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\CreatedAtEntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Traits\NameableEntityInterface;
use App\Traits\TimestampableEntityInterface;
use App\Traits\IdentifiableEntityInterface;

/**
 * Interface MeshConceptInterface
 */
interface MeshConceptInterface extends
    IdentifiableEntityInterface,
    NameableEntityInterface,
    TimestampableEntityInterface,
    CreatedAtEntityInterface
{
    /**
     * @param bool $preferred
     */
    public function setPreferred($preferred);

    /**
     * @return bool
     */
    public function getPreferred(): bool;

    /**
     * @param string $scopeNote
     */
    public function setScopeNote($scopeNote);

    /**
     * @return string
     */
    public function getScopeNote(): string;

    /**
     * @param string $casn1Name
     */
    public function setCasn1Name($casn1Name);

    /**
     * @return string
     */
    public function getCasn1Name(): string;

    /**
     * @param string $registryNumber
     */
    public function setRegistryNumber($registryNumber);

    /**
     * @return string
     */
    public function getRegistryNumber(): string;

    public function setTerms(Collection $terms);

    public function addTerm(MeshTermInterface $term);

    public function removeTerm(MeshTermInterface $term);

    /**
     * @return ArrayCollection|MeshTermInterface[]
     */
    public function getTerms(): Collection;

    public function setDescriptors(Collection $descriptors);

    public function addDescriptor(MeshDescriptorInterface $descriptor);

    public function removeDescriptor(MeshDescriptorInterface $descriptor);

    /**
     * @return ArrayCollection|MeshDescriptorInterface[]
     */
    public function getDescriptors(): Collection;
}
