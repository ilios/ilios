<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\ApiKeyInterface;

/**
 * Interface ApiKeyManagerInterface
 */
interface ApiKeyManagerInterface
{
    /** 
     *@return ApiKeyInterface
     */
    public function createApiKey();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return ApiKeyInterface
     */
    public function findApiKeyBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ApiKeyInterface[]|Collection
     */
    public function findApiKeysBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param ApiKeyInterface $apiKey
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateApiKey(ApiKeyInterface $apiKey, $andFlush = true);

    /**
     * @param ApiKeyInterface $apiKey
     *
     * @return void
     */
    public function deleteApiKey(ApiKeyInterface $apiKey);

    /**
     * @return string
     */
    public function getClass();
}
