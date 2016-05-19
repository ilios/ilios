<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\CurriculumInventoryInstitutionInterface;
use Ilios\CoreBundle\Entity\Manager\CurriculumInventoryInstitutionManagerInterface as BaseInterface;

/**
 * Class CurriculumInventoryInstitutionManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class CurriculumInventoryInstitutionManager extends BaseManager implements BaseInterface
{
    /**
     * {@inheritdoc}
     */
    public function findCurriculumInventoryInstitutionBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findCurriculumInventoryInstitutionsBy(
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
    public function updateCurriculumInventoryInstitution(
        CurriculumInventoryInstitutionInterface $curriculumInventoryInstitution,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($curriculumInventoryInstitution);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($curriculumInventoryInstitution));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteCurriculumInventoryInstitution(
        CurriculumInventoryInstitutionInterface $curriculumInventoryInstitution
    ) {
        $this->em->remove($curriculumInventoryInstitution);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createCurriculumInventoryInstitution()
    {
        $class = $this->getClass();
        return new $class();
    }
}
