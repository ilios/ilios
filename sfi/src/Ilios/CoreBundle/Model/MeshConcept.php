<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

use Ilios\CoreBundle\Traits\IdentifiableTrait;
use Ilios\CoreBundle\Traits\NameableTrait;

/**
 * Class MeshConcept
 * @package Ilios\CoreBundle\Model
 *
 * @ORM\Entity
 * @ORM\Table(name="product")
 */
class MeshConcept implements MeshConceptInterface
{
    use IdentifiableTrait;
    use NameableTrait;
    use TimestampableEntity;

    /**
     * @var string
     */
    protected $umlsUid;

    /**
     * @var boolean
     */
    protected $preferred;

    /**
     * @var string
     */
    protected $scopeNote;

    /**
     * @var string
     */
    protected $casn1Name;

    /**
     * @var string
     */
    protected $registryNumber;

    /**
     * @param string $umlsUid
     */
    public function setUmlsUid($umlsUid)
    {
        $this->umlsUid = $umlsUid;
    }

    /**
     * @return string
     */
    public function getUmlsUid()
    {
        return $this->umlsUid;
    }

    /**
     * @param boolean $preferred
     */
    public function setPreferred($preferred)
    {
        $this->preferred = $preferred;
    }

    /**
     * @return boolean
     */
    public function getPreferred()
    {
        return $this->preferred;
    }

    /**
     * @param string $scopeNote
     */
    public function setScopeNote($scopeNote)
    {
        $this->scopeNote = $scopeNote;
    }

    /**
     * @return string
     */
    public function getScopeNote()
    {
        return $this->scopeNote;
    }

    /**
     * @param string $casn1Name
     */
    public function setCasn1Name($casn1Name)
    {
        $this->casn1Name = $casn1Name;
    }

    /**
     * @return string
     */
    public function getCasn1Name()
    {
        return $this->casn1Name;
    }

    /**
     * @param string $registryNumber
     */
    public function setRegistryNumber($registryNumber)
    {
        $this->registryNumber = $registryNumber;
    }

    /**
     * @return string
     */
    public function getRegistryNumber()
    {
        return $this->registryNumber;
    }
}
