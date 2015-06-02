<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\OfferingInterface;

/**
 * Class OfferingManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class OfferingManager implements OfferingManagerInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $class;

    /**
     * @param Registry $em
     * @param string $class
     */
    public function __construct(Registry $em, $class)
    {
        $this->em         = $em->getManagerForClass($class);
        $this->class      = $class;
        $this->repository = $em->getRepository($class);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return OfferingInterface
     */
    public function findOfferingBy(
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
     * @return ArrayCollection|OfferingInterface[]
     */
    public function findOfferingsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param OfferingInterface $offering
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateOffering(
        OfferingInterface $offering,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($offering);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($offering));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param OfferingInterface $offering
     */
    public function deleteOffering(
        OfferingInterface $offering
    ) {
        $this->em->remove($offering);
        $this->em->flush();
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return OfferingInterface
     */
    public function createOffering()
    {
        $class = $this->getClass();
        return new $class();
    }
}
