<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockInterface;
use Ilios\CoreBundle\Entity\Manager\CurriculumInventorySequenceBlockManagerInterface as BaseInterface;

/**
 * Class CurriculumInventorySequenceBlockManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class CurriculumInventorySequenceBlockManager extends AbstractManager implements BaseInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CurriculumInventorySequenceBlockInterface
     */
    public function findCurriculumInventorySequenceBlockBy(
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
     * @return ArrayCollection|CurriculumInventorySequenceBlockInterface[]
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
     * @param CurriculumInventorySequenceBlockInterface $curriculumInventorySequenceBlock
     * @param bool $andFlush
     * @param bool $forceId
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
     * @param CurriculumInventorySequenceBlockInterface $curriculumInventorySequenceBlock
     */
    public function deleteCurriculumInventorySequenceBlock(
        CurriculumInventorySequenceBlockInterface $curriculumInventorySequenceBlock
    ) {
        $this->em->remove($curriculumInventorySequenceBlock);
        $this->em->flush();
    }

    /**
     * @return CurriculumInventorySequenceBlockInterface
     */
    public function createCurriculumInventorySequenceBlock()
    {
        $class = $this->getClass();
        return new $class();
    }
}
