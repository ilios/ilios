<?php

namespace Ilios\CoreBundle\Model;

use Ilios\CoreBundle\Traits\NameableEntityInterface;
use Ilios\CoreBundle\Traits\TimestampableEntityinterface;
use Ilios\CoreBundle\Traits\UniversallyUniqueEntityInterface;

/**
 * Interface MeshConceptInterface
 * @package Ilios\CoreBundle\Model
 */
interface MeshConceptInterface extends
    UniversallyUniqueEntityInterface,
    NameableEntityInterface,
    TimestampableEntityinterface
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

