<?php

namespace Ilios\CoreBundle\Entity;

use Ilios\CoreBundle\Traits\NameableEntityInterface;
use Ilios\CoreBundle\Traits\TimestampableEntityInterface;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;

/**
 * Interface MeshConceptInterface
 * @package Ilios\CoreBundle\Entity
 */
interface MeshConceptInterface extends
    IdentifiableEntityInterface,
    NameableEntityInterface,
    TimestampableEntityInterface,
    LoggableEntityInterface
{
    /**
     * @param string $umlsUid
     */
    public function setUmlsUid($umlsUid);

    /**
     * @return string
     */
    public function getUmlsUid();

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
}
