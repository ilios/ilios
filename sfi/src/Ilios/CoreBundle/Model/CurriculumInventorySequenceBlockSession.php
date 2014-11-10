<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\ORM\Mapping as ORM;

use Ilios\CoreBundle\Traits\IdentifiableEntity;

/**
 * Class CurriculumInventorySequenceBlockSession
 * @package Ilios\CoreBundle\Model
 *
 * @ORM\Entity
 * @ORM\Table(name="curriculum_inventory_sequence_block_session")
 */
class CurriculumInventorySequenceBlockSession implements CurriculumInventorySequenceBlockSessionInterface
{
//    use IdentifiableEntity;

    /**
     * @deprecated To be removed in 3.1, replaced by ID by enabling trait.
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="bigint", length=20, name="sequence_block_session_id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $sequenceBlockSessionId;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", length=4)
     */
    protected $countOfferingsOnce;

    /**
     * @var CurriculumInventorySequenceBlockInterface
     *
     * @ORM\ManyToOne(targetEntity="CurriculumInventorySequenceBlock", inversedBy="blockSessions")
     */
    protected $sequenceBlock;

    /**
     * @var SessionInterface
     *
     * @ORM\ManyToOne(targetEntity="Session", inversedBy="curriculumInventorySequenceBlockSessions")
     */
    protected $session;

    public function __construct()
    {
        //defaults
        $this->countOfferingsOnce = true;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->sequenceBlockSessionId = $id;
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return ($this->id === null) ? $this->sequenceBlockSessionId : $this->id;
    }

    /**
     * @param boolean $countOfferingsOnce
     */
    public function setCountOfferingsOnce($countOfferingsOnce)
    {
        $this->countOfferingsOnce = $countOfferingsOnce;
    }

    /**
     * @return boolean 
     */
    public function hasCountOfferingsOnce()
    {
        return $this->countOfferingsOnce;
    }

    /**
     * @param CurriculumInventorySequenceBlockInterface $sequenceBlock
     */
    public function setSequenceBlock(CurriculumInventorySequenceBlockInterface $sequenceBlock)
    {
        $this->sequenceBlock = $sequenceBlock;
    }

    /**
     * @return CurriculumInventorySequenceBlockInterface
     */
    public function getSequenceBlock()
    {
        return $this->sequenceBlock;
    }

    /**
     * @param SessionInterface $session
     */
    public function setSession(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @return SessionInterface
     */
    public function getSession()
    {
        return $this->session;
    }
}
