<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\UserSyncExceptionManager as BaseUserSyncExceptionManager;
use Ilios\CoreBundle\Model\UserSyncExceptionInterface;

class UserSyncExceptionManager extends BaseUserSyncExceptionManager
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
     * @return UserSyncExceptionInterface
     */
    public function findUserSyncExceptionBy(array $criteria, array $orderBy = null)
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
     * @return UserSyncExceptionInterface[]|Collection
     */
    public function findUserSyncExceptionsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param UserSyncExceptionInterface $userSyncException
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateUserSyncException(UserSyncExceptionInterface $userSyncException, $andFlush = true)
    {
        $this->em->persist($userSyncException);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param UserSyncExceptionInterface $userSyncException
     *
     * @return void
     */
    public function deleteUserSyncException(UserSyncExceptionInterface $userSyncException)
    {
        $this->em->remove($userSyncException);
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
