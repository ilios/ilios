<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\OfferingManager as BaseOfferingManager;
use Ilios\CoreBundle\Model\OfferingInterface;

class OfferingManager extends BaseOfferingManager
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
     * @param EntityManager $em
     * @param string $class
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em         = $em;
        $this->class      = $class;
        $this->repository = $em->getRepository($class);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return OfferingInterface
     */
    public function findOfferingBy(array $criteria, array $orderBy = null)
    {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * Previously known as findAllBy()
     *
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return OfferingInterface[]|Collection
     */
    public function findOfferingsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param OfferingInterface $offering
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateOffering(OfferingInterface $offering, $andFlush = true)
    {
        $this->em->persist($offering);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param OfferingInterface $offering
     *
     * @return void
     */
    public function deleteOffering(OfferingInterface $offering)
    {
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
}
