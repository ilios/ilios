<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Entity\LearningMaterialInterface;

class LearningMaterialRepository extends EntityRepository
{
    /**
     * Find all the file type learning materials
     */
    public function findFileLearningMaterials()
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->add('select', 'lm')->from('IliosCoreBundle:LearningMaterial', 'lm');
        $qb->where($qb->expr()->isNotNull('lm.relativePath'));

        return $qb->getQuery()->getResult();
    }

    /**
     * Find by a string query
     * @param string $q
     * @param integer $orderBy
     * @param integer $limit
     * @param integer $offset
     * @return LearningMaterialInterface[]
     */
    public function findByQ($q, $orderBy, $limit, $offset)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->add('select', 'lm')->from('IliosCoreBundle:LearningMaterial', 'lm');
        $terms = explode(' ', $q);
        $terms = array_filter($terms, 'strlen');
        if (empty($terms)) {
            return [];
        }

        foreach ($terms as $key => $term) {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('lm.title', "?{$key}"),
                $qb->expr()->like('lm.description', "?{$key}")
            ))
            ->setParameter($key, '%' . $term . '%');
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('lm.' . $sort, $order);
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
