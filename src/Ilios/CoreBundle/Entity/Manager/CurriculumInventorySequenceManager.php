<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceInterface;

/**
 * Class CurriculumInventorySequenceManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class CurriculumInventorySequenceManager extends AbstractManager implements CurriculumInventorySequenceManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CurriculumInventorySequenceInterface
     */
    public function findCurriculumInventorySequenceBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|CurriculumInventorySequenceInterface[]
     */
    public function findCurriculumInventorySequencesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param CurriculumInventorySequenceInterface $curriculumInventorySequence
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateCurriculumInventorySequence(
        CurriculumInventorySequenceInterface $curriculumInventorySequence,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($curriculumInventorySequence);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($curriculumInventorySequence));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param CurriculumInventorySequenceInterface $curriculumInventorySequence
     */
    public function deleteCurriculumInventorySequence(
        CurriculumInventorySequenceInterface $curriculumInventorySequence
    ) {
        $this->em->remove($curriculumInventorySequence);
        $this->em->flush();
    }

    /**
     * @return CurriculumInventorySequenceInterface
     */
    public function createCurriculumInventorySequence()
    {
        $class = $this->getClass();
        return new $class();
    }
}
