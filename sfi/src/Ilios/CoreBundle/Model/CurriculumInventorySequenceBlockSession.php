<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * CurriculumInventorySequenceBlockSession
 */
class CurriculumInventorySequenceBlockSession
{
    /**
     * @var integer
     */
    private $sequenceBlockSessionId;

    /**
     * @var boolean
     */
    private $countOfferingsOnce;

    /**
     * @var \Ilios\CoreBundle\Model\CurriculumInventorySequenceBlock
     */
    private $sequenceBlock;

    /**
     * @var \Ilios\CoreBundle\Model\Session
     */
    private $session;


    /**
     * Get sequenceBlockSessionId
     *
     * @return integer 
     */
    public function getSequenceBlockSessionId()
    {
        return $this->sequenceBlockSessionId;
    }

    /**
     * Set countOfferingsOnce
     *
     * @param boolean $countOfferingsOnce
     * @return CurriculumInventorySequenceBlockSession
     */
    public function setCountOfferingsOnce($countOfferingsOnce)
    {
        $this->countOfferingsOnce = $countOfferingsOnce;

        return $this;
    }

    /**
     * Get countOfferingsOnce
     *
     * @return boolean 
     */
    public function getCountOfferingsOnce()
    {
        return $this->countOfferingsOnce;
    }

    /**
     * Set sequenceBlock
     *
     * @param \Ilios\CoreBundle\Model\CurriculumInventorySequenceBlock $sequenceBlock
     * @return CurriculumInventorySequenceBlockSession
     */
    public function setSequenceBlock(\Ilios\CoreBundle\Model\CurriculumInventorySequenceBlock $sequenceBlock = null)
    {
        $this->sequenceBlock = $sequenceBlock;

        return $this;
    }

    /**
     * Get sequenceBlock
     *
     * @return \Ilios\CoreBundle\Model\CurriculumInventorySequenceBlock 
     */
    public function getSequenceBlock()
    {
        return $this->sequenceBlock;
    }

    /**
     * Set session
     *
     * @param \Ilios\CoreBundle\Model\Session $session
     * @return CurriculumInventorySequenceBlockSession
     */
    public function setSession(\Ilios\CoreBundle\Model\Session $session = null)
    {
        $this->session = $session;

        return $this;
    }

    /**
     * Get session
     *
     * @return \Ilios\CoreBundle\Model\Session 
     */
    public function getSession()
    {
        return $this->session;
    }
}
