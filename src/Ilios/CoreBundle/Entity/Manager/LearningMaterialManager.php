<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\LearningMaterialInterface;

/**
 * Class LearningMaterialManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class LearningMaterialManager extends BaseManager
{
    /**
     * @deprecated
     */
    public function findLearningMaterialBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findLearningMaterialsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

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
        return $this->getRepository()->findByQ($q, $orderBy, $limit, $offset);
    }

    /**
     * @deprecated
     */
    public function updateLearningMaterial(
        LearningMaterialInterface $learningMaterial,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($learningMaterial, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteLearningMaterial(
        LearningMaterialInterface $learningMaterial
    ) {
        $this->delete($learningMaterial);
    }

    /**
     * @deprecated
     */
    public function createLearningMaterial()
    {
        return $this->create();
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
        return $this->getRepository()->findFileLearningMaterials($limit, $offset);
    }

    /**
     * @return int
     */
    public function getTotalFileLearningMaterialCount()
    {
        $dql = 'SELECT COUNT(l.id) FROM IliosCoreBundle:LearningMaterial l WHERE l.relativePath IS NOT NULL';
        return $this->em
            ->createQuery($dql)->getSingleScalarResult();
    }

    /**
     * @return int
     */
    public function getTotalLearningMaterialCount()
    {
        return $this->em
            ->createQuery('SELECT COUNT(l.id) FROM IliosCoreBundle:LearningMaterial l')->getSingleScalarResult();
    }
}
