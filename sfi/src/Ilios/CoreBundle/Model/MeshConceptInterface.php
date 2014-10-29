<?php

namespace Ilios\CoreBundle\Model;

use Ilios\CoreBundle\Traits\IdentifiableTraitInterface;
use Ilios\CoreBundle\Traits\NameableTraitInterface;
use Ilios\CoreBundle\Traits\TimestampableTraitinterface;

/**
 * Interface MeshConceptInterface
 * @package Ilios\CoreBundle\Model
 */
interface MeshConceptInterface extends
    IdentifiableTraitInterface,
    NameableTraitInterface,
    TimestampableTraitinterface
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

