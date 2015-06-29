<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\AuthenticationInterface;
use Ilios\CoreBundle\Entity\Manager\AuthenticationManagerInterface;

/**
 * Class AuthenticationManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class AuthenticationManager implements AuthenticationManagerInterface
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
     * @return AuthenticationInterface
     */
    public function findAuthenticationBy(
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
     * @return ArrayCollection|AuthenticationInterface[]
     */
    public function findAuthenticationsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param string $username
     *
     * @return AuthenticationInterface
     */
    public function findAuthenticationByUsername($username)
    {
        $username = strtolower($username);
        return $this->repository->findOneByUsername($username);
    }

    /**
     * @param AuthenticationInterface $authentication
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateAuthentication(
        AuthenticationInterface $authentication,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($authentication);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($authentication));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param AuthenticationInterface $authentication
     */
    public function deleteAuthentication(
        AuthenticationInterface $authentication
    ) {
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

    /**
     * @return AuthenticationInterface
     */
    public function createAuthentication()
    {
        $class = $this->getClass();
        return new $class();
    }
}
