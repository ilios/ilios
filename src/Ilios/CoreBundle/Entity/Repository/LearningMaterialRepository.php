<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

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
}
