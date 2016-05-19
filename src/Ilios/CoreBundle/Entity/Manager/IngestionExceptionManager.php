<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\IngestionExceptionInterface;

/**
 * Class IngestionExceptionManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class IngestionExceptionManager extends BaseManager implements IngestionExceptionManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findIngestionExceptionBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findIngestionExceptionsBy(
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
     * {@inheritdoc}
     */
    public function deleteIngestionException(
        IngestionExceptionInterface $ingestionException
    ) {
        $this->em->remove($ingestionException);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createIngestionException()
    {
        $class = $this->getClass();
        return new $class();
    }
}
