<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Entity\SessionInterface;

/**
 * Class SessionsEntity
 * @package Ilios\CoreBundle\Traits
 */
trait SessionsEntity
{
    /**
     * @param Collection $sessions
     */
    public function setSessions(Collection $sessions)
    {
        $this->sessions = new ArrayCollection();

        foreach ($sessions as $session) {
            $this->addSession($session);
        }
    }

    /**
     * @param SessionInterface $session
     */
    public function addSession(SessionInterface $session)
    {
        $this->sessions->add($session);
    }

    /**
    * @return SessionInterface[]|ArrayCollection
    */
    public function getSessions()
    {
        //criteria not 100% reliale on many to many relationships
        //fix in https://github.com/doctrine/doctrine2/pull/1399
        // $criteria = Criteria::create()->where(Criteria::expr()->eq("deleted", false));
        // return new ArrayCollection($this->sessions->matching($criteria)->getValues());
        
        $arr = $this->sessions->filter(function ($entity) {
            return !$entity->isDeleted();
        })->toArray();
        
        $reIndexed = array_values($arr);
        
        return new ArrayCollection($reIndexed);
    }
}
