<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\IngestionExceptionInterface;

/**
 * Class IngestionExceptionManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class IngestionExceptionManager extends AbstractManager implements IngestionExceptionManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return IngestionExceptionInterface
     */
    public function findIngestionExceptionBy(
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
     * @return ArrayCollection|IngestionExceptionInterface[]
     */
    public function findIngestionExceptionsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param IngestionExceptionInterface $ingestionException
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateIngestionException(
        IngestionExceptionInterface $ingestionException,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($ingestionException);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($ingestionException));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param IngestionExceptionInterface $ingestionException
     */
    public function deleteIngestionException(
        IngestionExceptionInterface $ingestionException
    ) {
        $this->em->remove($ingestionException);
        $this->em->flush();
    }

    /**
     * @return IngestionExceptionInterface
     */
    public function createIngestionException()
    {
        $class = $this->getClass();
        return new $class();
    }
}
