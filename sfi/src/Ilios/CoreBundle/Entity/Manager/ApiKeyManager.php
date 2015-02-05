<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\ApiKeyInterface;

/**
 * ApiKey manager service.
 * Class ApiKeyManager
 * @package Ilios\CoreBundle\Manager
 */
class ApiKeyManager implements ApiKeyManagerInterface
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
     * @return ApiKeyInterface
     */
    public function findApiKeyBy(array $criteria, array $orderBy = null)
    {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ApiKeyInterface[]|Collection
     */
    public function findApiKeysBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param ApiKeyInterface $apiKey
     * @param bool $andFlush
     */
    public function updateApiKey(ApiKeyInterface $apiKey, $andFlush = true)
    {
        $this->em->persist($apiKey);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param ApiKeyInterface $apiKey
     */
    public function deleteApiKey(ApiKeyInterface $apiKey)
    {
        $this->em->remove($apiKey);
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
     * @return ApiKeyInterface
     */
    public function createApiKey()
    {
        $class = $this->getClass();
        return new $class();
    }
}
