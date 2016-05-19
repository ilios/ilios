<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceInterface;

/**
 * Class CurriculumInventorySequenceManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class CurriculumInventorySequenceManager extends BaseManager implements CurriculumInventorySequenceManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findCurriculumInventorySequenceBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findCurriculumInventorySequencesBy(
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
     * {@inheritdoc}
     */
    public function deleteCurriculumInventorySequence(
        CurriculumInventorySequenceInterface $curriculumInventorySequence
    ) {
        $this->em->remove($curriculumInventorySequence);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createCurriculumInventorySequence()
    {
        $class = $this->getClass();
        return new $class();
    }
}
