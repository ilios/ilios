<?php

namespace App\Entity\Manager;

use App\Entity\LearningMaterialInterface;
use App\Entity\Repository\LearningMaterialRepository;

/**
 * Class LearningMaterialManager
 */
class LearningMaterialManager extends BaseManager
{
    /**
     * Use a query term to find learning materials
     *
     * @param string $q
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return LearningMaterialInterface[]
     */
    public function findLearningMaterialsByQ(
        $q,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        /** @var LearningMaterialRepository $repository */
        $repository = $this->getRepository();
        return $repository->findByQ($q, $orderBy, $limit, $offset);
    }

    /**
     * Find all the File type learning materials
     * @param integer $limit
     * @param integer $offset
     *
     * @return LearningMaterialInterface[]
     */
    public function findFileLearningMaterials($limit, $offset)
    {
        /** @var LearningMaterialRepository $repository */
        $repository = $this->getRepository();
        return $repository->findFileLearningMaterials($limit, $offset);
    }

    /**
     * @return int
     */
    public function getTotalFileLearningMaterialCount()
    {
        $dql = 'SELECT COUNT(l.id) FROM AppBundle:LearningMaterial l WHERE l.relativePath IS NOT NULL';
        return $this->em
            ->createQuery($dql)->getSingleScalarResult();
    }

    /**
     * @return int
     */
    public function getTotalLearningMaterialCount()
    {
        return $this->em
            ->createQuery('SELECT COUNT(l.id) FROM AppBundle:LearningMaterial l')->getSingleScalarResult();
    }
}
