<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockInterface;
use Ilios\CoreBundle\Entity\Manager\CurriculumInventorySequenceBlockManagerInterface as BaseInterface;

/**
 * Class CurriculumInventorySequenceBlockManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class CurriculumInventorySequenceBlockManager extends BaseManager implements BaseInterface
{
    /**
     * {@inheritdoc}
     */
    public function findCurriculumInventorySequenceBlockBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findCurriculumInventorySequenceBlocksBy(
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
    public function updateCurriculumInventorySequenceBlock(
        CurriculumInventorySequenceBlockInterface $curriculumInventorySequenceBlock,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($curriculumInventorySequenceBlock);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($curriculumInventorySequenceBlock));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteCurriculumInventorySequenceBlock(
        CurriculumInventorySequenceBlockInterface $curriculumInventorySequenceBlock
    ) {
        $this->em->remove($curriculumInventorySequenceBlock);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createCurriculumInventorySequenceBlock()
    {
        $class = $this->getClass();
        return new $class();
    }
}
