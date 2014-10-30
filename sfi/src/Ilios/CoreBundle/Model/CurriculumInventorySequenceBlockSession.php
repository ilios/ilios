<?php

namespace Ilios\CoreBundle\Model;

use Ilios\CoreBundle\Traits\IdentifiableEntity;

/**
 * Class CurriculumInventorySequenceBlockSession
 * @package Ilios\CoreBundle\Model
 */
class CurriculumInventorySequenceBlockSession implements CurriculumInventorySequenceBlockSessionInterface
{
    use IdentifiableEntity;

    /**
     * @var boolean
     */
    protected $countOfferingsOnce;

    /**
     * @var CurriculumInventorySequenceBlockInterface
     */
    protected $sequenceBlock;

    /**
     * @var SessionInterface
     */
    protected $session;

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
