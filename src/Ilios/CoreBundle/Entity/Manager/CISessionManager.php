<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\CISessionInterface;

/**
 * Class CISessionManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class CISessionManager extends AbstractManager implements CISessionManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CISessionInterface
     */
    public function findCISessionBy(
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
     * @return ArrayCollection|CISessionInterface[]
     */
    public function findCISessionsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param CISessionInterface $cISession
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateCISession(
        CISessionInterface $cISession,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($cISession);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($cISession));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param CISessionInterface $cISession
     */
    public function deleteCISession(
        CISessionInterface $cISession
    ) {
        $this->em->remove($cISession);
        $this->em->flush();
    }

    /**
     * @return CISessionInterface
     */
    public function createCISession()
    {
        $class = $this->getClass();
        return new $class();
    }
}
