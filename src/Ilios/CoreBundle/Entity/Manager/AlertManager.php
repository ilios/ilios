<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\AlertInterface;

/**
 * Class AlertManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class AlertManager extends BaseManager implements AlertManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findAlertBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findAlertsBy(
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
    public function updateAlert(
        AlertInterface $alert,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($alert);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($alert));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAlert(
        AlertInterface $alert
    ) {
        $this->em->remove($alert);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createAlert()
    {
        $class = $this->getClass();
        return new $class();
    }
}
