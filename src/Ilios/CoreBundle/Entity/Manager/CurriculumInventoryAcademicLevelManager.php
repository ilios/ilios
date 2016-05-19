<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevelInterface;
use Ilios\CoreBundle\Entity\Manager\CurriculumInventoryAcademicLevelManagerInterface as BaseInterface;

/**
 * Class CurriculumInventoryAcademicLevelManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class CurriculumInventoryAcademicLevelManager extends BaseManager implements BaseInterface
{
    /**
     * {@inheritdoc}
     */
    public function findCurriculumInventoryAcademicLevelBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findCurriculumInventoryAcademicLevelsBy(
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
    public function updateCurriculumInventoryAcademicLevel(
        CurriculumInventoryAcademicLevelInterface $curriculumInventoryAcademicLevel,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($curriculumInventoryAcademicLevel);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($curriculumInventoryAcademicLevel));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteCurriculumInventoryAcademicLevel(
        CurriculumInventoryAcademicLevelInterface $curriculumInventoryAcademicLevel
    ) {
        $this->em->remove($curriculumInventoryAcademicLevel);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createCurriculumInventoryAcademicLevel()
    {
        $class = $this->getClass();
        return new $class();
    }
}
