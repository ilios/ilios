<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\AamcMethodManager as BaseAamcMethodManager;
use Ilios\CoreBundle\Model\AamcMethodInterface;

class AamcMethodManager extends BaseAamcMethodManager
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
     * @return AamcMethodInterface
     */
    public function findAamcMethodBy(array $criteria, array $orderBy = null)
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
     * @return AamcMethodInterface[]|Collection
     */
    public function findAamcMethodsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param AamcMethodInterface $aamcMethod
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateAamcMethod(AamcMethodInterface $aamcMethod, $andFlush = true)
    {
        $this->em->persist($aamcMethod);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param AamcMethodInterface $aamcMethod
     *
     * @return void
     */
    public function deleteAamcMethod(AamcMethodInterface $aamcMethod)
    {
        $this->em->remove($aamcMethod);
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
