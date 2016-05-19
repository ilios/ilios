<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockSessionInterface;
use Ilios\CoreBundle\Entity\Manager\CurriculumInventorySequenceBlockSessionManagerInterface as BaseInterface;

/**
 * Class CurriculumInventorySequenceBlockSessionManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class CurriculumInventorySequenceBlockSessionManager extends BaseManager implements BaseInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CurriculumInventorySequenceBlockSessionInterface
     */
    public function findCurriculumInventorySequenceBlockSessionBy(
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
     * @return CurriculumInventorySequenceBlockSessionInterface[]
     */
    public function findCurriculumInventorySequenceBlockSessionsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateCurriculumInventorySequenceBlockSession(
        CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($curriculumInventorySequenceBlockSession);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($curriculumInventorySequenceBlockSession));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession
     */
    public function deleteCurriculumInventorySequenceBlockSession(
        CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession
    ) {
        $this->em->remove($curriculumInventorySequenceBlockSession);
        $this->em->flush();
    }

    /**
     * @return CurriculumInventorySequenceBlockSessionInterface
     */
    public function createCurriculumInventorySequenceBlockSession()
    {
        $class = $this->getClass();
        return new $class();
    }
}
