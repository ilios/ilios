<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Type as DoctrineType;
use Doctrine\DBAL\Query\QueryBuilder;

class MeshDescriptorRepository extends EntityRepository
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
        $qb->add('select', 'd')->from('IliosCoreBundle:MeshDescriptor', 'd');
        $qb->leftJoin('d.previousIndexing', 'pi');
        $qb->leftJoin('d.concepts', 'c');
        $qb->leftJoin('c.semanticTypes', 'st');
        $qb->leftJoin('c.terms', 't');
        $terms = explode(' ', $q);
        $terms = array_filter($terms, 'strlen');
        if (empty($terms)) {
            return new ArrayCollection([]);
        }

        foreach ($terms as $key => $term) {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('d.id', "?{$key}"),
                $qb->expr()->like('d.annotation', "?{$key}"),
                $qb->expr()->like('pi.previousIndexing', "?{$key}"),
                $qb->expr()->like('st.name', "?{$key}"),
                $qb->expr()->like('t.name', "?{$key}")
            ))
            ->setParameter($key, '%' . $term . '%');
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('d.' . $sort, $order);
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
