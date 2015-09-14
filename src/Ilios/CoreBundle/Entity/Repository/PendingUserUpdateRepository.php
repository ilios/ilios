<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Type as DoctrineType;
use Doctrine\DBAL\Query\QueryBuilder;
use Ilios\CoreBundle\Classes\UserEvent;

class PendingUserUpdateRepository extends EntityRepository
{
    
    /**
     * Remove all pending user updates from the database
     */
    public function removeAllPendingUserUpdates()
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->delete('IliosCoreBundle:PendingUserUpdate', 'p');
        $qb->getQuery()->execute();
    }
}
