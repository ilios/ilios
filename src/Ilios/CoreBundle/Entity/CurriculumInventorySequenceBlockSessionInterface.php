<?php

namespace Ilios\CoreBundle\Entity;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;

/**
 * Interface CurriculumInventorySequenceBlockSessionInterface
 */
interface CurriculumInventorySequenceBlockSessionInterface extends
    IdentifiableEntityInterface,
    LoggableEntityInterface
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
