<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;

class UserRepository extends EntityRepository
{
    /**
     * Find by a string query
     * @param string $q
     * @param integer $orderBy
     * @param integer $limit
     * @param offset $offset
     */
    public function findByQ($q, $orderBy, $limit, $offset)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->add('select', 'u')->from('IliosCoreBundle:User', 'u');
        $terms = explode(' ', $q);
        $terms = array_filter($terms, 'strlen');
        if (empty($terms)) {
            return new ArrayCollection([]);
        }

        foreach ($terms as $key => $term) {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('u.firstName', "?{$key}"),
                $qb->expr()->like('u.lastName', "?{$key}"),
                $qb->expr()->like('u.middleName', "?{$key}"),
                $qb->expr()->like('u.email', "?{$key}")
            ))
            ->setParameter($key, '%' . $term . '%');
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('u.' . $sort, $order);
            }
        }

        if ($offset) {
            $qb->setFirstResult($offset);
        }

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }
}
