<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevelInterface;
use Ilios\CoreBundle\Entity\Manager\CurriculumInventoryAcademicLevelManagerInterface as BaseInterface;

/**
 * Class CurriculumInventoryAcademicLevelManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class CurriculumInventoryAcademicLevelManager extends AbstractManager implements BaseInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CurriculumInventoryAcademicLevelInterface
     */
    public function findCurriculumInventoryAcademicLevelBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|CurriculumInventoryAcademicLevelInterface[]
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
     * @param CurriculumInventoryAcademicLevelInterface $curriculumInventoryAcademicLevel
     * @param bool $andFlush
     * @param bool $forceId
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
     * @param CurriculumInventoryAcademicLevelInterface $curriculumInventoryAcademicLevel
     */
    public function deleteCurriculumInventoryAcademicLevel(
        CurriculumInventoryAcademicLevelInterface $curriculumInventoryAcademicLevel
    ) {
        $this->em->remove($curriculumInventoryAcademicLevel);
        $this->em->flush();
    }

    /**
     * @return CurriculumInventoryAcademicLevelInterface
     */
    public function createCurriculumInventoryAcademicLevel()
    {
        $class = $this->getClass();
        return new $class();
    }

    /**
     * @todo
     */
    public function getAppliedLevels($reportId)
    {
        // TODO: Implement getAppliedLevels() method.
    }
}
