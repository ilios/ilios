<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\ApiKeyInterface;

/**
 * Interface ApiKeyManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface ApiKeyManagerInterface extends ManagerInterface
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
    );

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
    );

    /**
     * @param ApiKeyInterface $apiKey
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateApiKey(
        ApiKeyInterface $apiKey,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param ApiKeyInterface $apiKey
     *
     * @return void
     */
    public function deleteApiKey(
        ApiKeyInterface $apiKey
    );

    /**
     * @return ApiKeyInterface
     */
    public function createApiKey();
}
