<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\CurriculumInventoryInstitutionInterface;
use Ilios\CoreBundle\Entity\Manager\CurriculumInventoryInstitutionManagerInterface as BaseInterface;

/**
 * Class CurriculumInventoryInstitutionManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class CurriculumInventoryInstitutionManager extends AbstractManager implements BaseInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CurriculumInventoryInstitutionInterface
     */
    public function findCurriculumInventoryInstitutionBy(
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
     * @return ArrayCollection|CurriculumInventoryInstitutionInterface[]
     */
    public function findCurriculumInventoryInstitutionsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param CurriculumInventoryInstitutionInterface $curriculumInventoryInstitution
     * @param bool $andFlush
     * @param bool $forceId
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
     * @param CurriculumInventoryInstitutionInterface $curriculumInventoryInstitution
     */
    public function deleteCurriculumInventoryInstitution(
        CurriculumInventoryInstitutionInterface $curriculumInventoryInstitution
    ) {
        $this->em->remove($curriculumInventoryInstitution);
        $this->em->flush();
    }

    /**
     * @return CurriculumInventoryInstitutionInterface
     */
    public function createCurriculumInventoryInstitution()
    {
        $class = $this->getClass();
        return new $class();
    }
}
