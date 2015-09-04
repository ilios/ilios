<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\AlertChangeTypeInterface;

/**
 * Class AlertChangeTypeManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class AlertChangeTypeManager extends AbstractManager implements AlertChangeTypeManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return AlertChangeTypeInterface
     */
    public function findAlertChangeTypeBy(
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
     * @return ArrayCollection|AlertChangeTypeInterface[]
     */
    public function findAlertChangeTypesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param AlertChangeTypeInterface $alertChangeType
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateAlertChangeType(
        AlertChangeTypeInterface $alertChangeType,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($alertChangeType);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($alertChangeType));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param AlertChangeTypeInterface $alertChangeType
     */
    public function deleteAlertChangeType(
        AlertChangeTypeInterface $alertChangeType
    ) {
        $this->em->remove($alertChangeType);
        $this->em->flush();
    }

    /**
     * @return AlertChangeTypeInterface
     */
    public function createAlertChangeType()
    {
        $class = $this->getClass();
        return new $class();
    }
}
