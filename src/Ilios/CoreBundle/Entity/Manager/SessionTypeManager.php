<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\SessionTypeInterface;

/**
 * Class SessionTypeManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class SessionTypeManager extends BaseManager implements SessionTypeManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findSessionTypeBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findSessionTypesBy(
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
    public function updateSessionType(
        SessionTypeInterface $sessionType,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($sessionType);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($sessionType));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSessionType(
        SessionTypeInterface $sessionType
    ) {
        $this->em->remove($sessionType);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createSessionType()
    {
        $class = $this->getClass();
        return new $class();
    }
}
