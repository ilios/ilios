<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\AlertChangeTypeInterface;

/**
 * Class AlertChangeTypeManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class AlertChangeTypeManager extends BaseManager implements AlertChangeTypeManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findAlertChangeTypeBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function deleteAlertChangeType(
        AlertChangeTypeInterface $alertChangeType
    ) {
        $this->em->remove($alertChangeType);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createAlertChangeType()
    {
        $class = $this->getClass();
        return new $class();
    }
}
