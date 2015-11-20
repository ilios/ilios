<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\CourseLearningMaterialInterface;

/**
 * Class CourseLearningMaterialManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class CourseLearningMaterialManager extends AbstractManager implements CourseLearningMaterialManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findCourseLearningMaterialBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findCourseLearningMaterialsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function updateCourseLearningMaterial(
        CourseLearningMaterialInterface $courseLearningMaterial,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($courseLearningMaterial);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($courseLearningMaterial));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteCourseLearningMaterial(
        CourseLearningMaterialInterface $courseLearningMaterial
    ) {
        $this->em->remove($courseLearningMaterial);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createCourseLearningMaterial()
    {
        $class = $this->getClass();
        return new $class();
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalCourseLearningMaterialCount()
    {
        return $this->em->createQuery('SELECT COUNT(l.id) FROM IliosCoreBundle:CourseLearningMaterial l')
            ->getSingleScalarResult();
    }
}
