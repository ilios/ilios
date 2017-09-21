<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Traits\NameableEntityInterface;
use Ilios\CoreBundle\Traits\TimestampableEntityInterface;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;

/**
 * Interface MeshConceptInterface
 */
interface MeshConceptInterface extends
    IdentifiableEntityInterface,
    NameableEntityInterface,
    TimestampableEntityInterface
{
    /**
     * @param boolean $preferred
     */
    public function setPreferred($preferred);

    /**
     * @return boolean
     */
    public function getPreferred();

    /**
     * @param string $scopeNote
     */
    public function setScopeNote($scopeNote);

    /**
     * @return string
     */
    public function getScopeNote();

    /**
     * @param string $casn1Name
     */
    public function setCasn1Name($casn1Name);

    /**
     * @return string
     */
    public function getCasn1Name();

    /**
     * @param string $registryNumber
     */
    public function setRegistryNumber($registryNumber);

    /**
     * @return string
     */
    public function getRegistryNumber();

    /**
     * @param Collection $terms
     */
    public function setTerms(Collection $terms);

    /**
     * @param MeshTermInterface $term
     */
    public function addTerm(MeshTermInterface $term);

    /**
     * @param MeshTermInterface $term
     */
    public function removeTerm(MeshTermInterface $term);

    /**
     * @return ArrayCollection|MeshTermInterface[]
     */
    public function getTerms();

    /**
     * @param Collection $descriptors
     */
    public function setDescriptors(Collection $descriptors);

    /**
     * @param MeshDescriptorInterface $descriptor
     */
    public function addDescriptor(MeshDescriptorInterface $descriptor);

    /**
     * @param MeshDescriptorInterface $descriptor
     */
    public function removeDescriptor(MeshDescriptorInterface $descriptor);

    /**
     * @return ArrayCollection|MeshDescriptorInterface[]
     */
    public function getDescriptors();
}
