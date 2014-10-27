<?php

namespace Ilios\CoreBundle\Model;

use Ilios\CoreBundle\Traits\IdentifiableTraitInterface;

/**
 * Interface CurriculumInventorySequenceBlockSessionInterface
 */
interface CurriculumInventorySequenceBlockSessionInterface extends IdentifiableTraitInterface
{
    /**
     * @param boolean $countOfferingsOnce
     */
    public function setCountOfferingsOnce($countOfferingsOnce);

    /**
     * @return boolean
     */
    public function hasCountOfferingsOnce();

    /**
     * @param CurriculumInventorySequenceBlockInterface $sequenceBlock
     */
    public function setSequenceBlock(CurriculumInventorySequenceBlockInterface $sequenceBlock);

    /**
     * @return CurriculumInventorySequenceBlockInterface
     */
    public function getSequenceBlock();

    /**
     * @param SessionInterface $session
     */
    public function setSession(SessionInterface $session);

    /**
     * @return SessionInterface
     */
    public function getSession();
}

