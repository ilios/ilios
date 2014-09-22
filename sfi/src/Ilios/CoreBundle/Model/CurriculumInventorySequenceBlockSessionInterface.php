<?php

namespace Ilios\CoreBundle\Model;



/**
 * Interface CurriculumInventorySequenceBlockSessionInterface
 */
interface CurriculumInventorySequenceBlockSessionInterface 
{
    public function getSequenceBlockSessionId();

    public function setCountOfferingsOnce($countOfferingsOnce);

    public function getCountOfferingsOnce();

    public function setSequenceBlock(\Ilios\CoreBundle\Model\CurriculumInventorySequenceBlock $sequenceBlock = null);

    public function getSequenceBlock();

    public function setSession(\Ilios\CoreBundle\Model\Session $session = null);

    public function getSession();
}
