<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

use Ilios\CoreBundle\Entity\AuthenticationInterface;

class AuthenticationRepository extends EntityRepository
{
    /**
     * Get an authentication entity by case insensitve user name
     * @param  string $username
     * @return AuthenticationInterface
     */
    public function findOneByUsername($username)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->add('select', 'a')->from('IliosCoreBundle:Authentication', 'a');
        $qb->where($qb->expr()->like('u.username', "?1"));
        $qb->setParameter(1, '%' . $username . '%');
        $result = $this->getEntityManager()
            ->createQuery($dql)->getSingleResult();

        return $qb->getQuery()->getSingleResult();
    }
}
