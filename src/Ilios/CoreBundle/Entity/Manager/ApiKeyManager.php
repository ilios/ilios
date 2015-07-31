<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\ApiKeyInterface;

/**
 * Class ApiKeyManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class ApiKeyManager extends AbstractManager implements ApiKeyManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return ApiKeyInterface
     */
    public function findApiKeyBy(
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
     * @return ArrayCollection|ApiKeyInterface[]
     */
    public function findApiKeysBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param ApiKeyInterface $apiKey
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateApiKey(
        ApiKeyInterface $apiKey,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($apiKey);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($apiKey));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param ApiKeyInterface $apiKey
     */
    public function deleteApiKey(
        ApiKeyInterface $apiKey
    ) {
        $this->em->remove($apiKey);
        $this->em->flush();
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
