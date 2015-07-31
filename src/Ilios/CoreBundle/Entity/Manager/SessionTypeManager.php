<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\SessionTypeInterface;

/**
 * Class SessionTypeManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class SessionTypeManager extends AbstractManager implements SessionTypeManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return SessionTypeInterface
     */
    public function findSessionTypeBy(
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
     * @return ArrayCollection|SessionTypeInterface[]
     */
    public function findSessionTypesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param SessionTypeInterface $sessionType
     * @param bool $andFlush
     * @param bool $forceId
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
     * @param SessionTypeInterface $sessionType
     */
    public function deleteSessionType(
        SessionTypeInterface $sessionType
    ) {
        $this->em->remove($sessionType);
        $this->em->flush();
    }

    /**
     * @return SessionTypeInterface
     */
    public function createSessionType()
    {
        $class = $this->getClass();
        return new $class();
    }
}
