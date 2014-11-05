<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\AuthenticationManager as BaseAuthenticationManager;
use Ilios\CoreBundle\Model\AuthenticationInterface;

class AuthenticationManager extends BaseAuthenticationManager
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
     * @return AuthenticationInterface
     */
    public function findAuthenticationBy(array $criteria, array $orderBy = null)
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
     * @return AuthenticationInterface[]|Collection
     */
    public function findAuthenticationsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param AuthenticationInterface $authentication
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateAuthentication(AuthenticationInterface $authentication, $andFlush = true)
    {
        $this->em->persist($authentication);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param AuthenticationInterface $authentication
     *
     * @return void
     */
    public function deleteAuthentication(AuthenticationInterface $authentication)
    {
        $this->em->remove($authentication);
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
