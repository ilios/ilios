<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\CurriculumInventoryExportInterface;

/**
 * Class CurriculumInventoryExportManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class CurriculumInventoryExportManager extends BaseManager implements CurriculumInventoryExportManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findCurriculumInventoryExportBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findCurriculumInventoryExportsBy(
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
    public function updateCurriculumInventoryExport(
        CurriculumInventoryExportInterface $curriculumInventoryExport,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($curriculumInventoryExport);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($curriculumInventoryExport));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteCurriculumInventoryExport(
        CurriculumInventoryExportInterface $curriculumInventoryExport
    ) {
        $this->em->remove($curriculumInventoryExport);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createCurriculumInventoryExport()
    {
        $class = $this->getClass();
        return new $class();
    }
}
