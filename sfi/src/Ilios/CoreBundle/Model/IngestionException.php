<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * IngestionException
 */
class IngestionException
{
    /**
     * @var integer
     */
    private $userId;

    /**
     * @var string
     */
    private $ingestedWideUid;
    
    /**
     * @var \Ilios\CoreBundle\Model\User
     */
    private $user;

    /**
     * Set userId
     *
     * @param int $userId
     * @return IngestionException
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set ingestedWideUid
     *
     * @param string $ingestedWideUid
     * @return IngestionException
     */
    public function setIngestedWideUid($ingestedWideUid)
    {
        $this->ingestedWideUid = $ingestedWideUid;

        return $this;
    }

    /**
     * Get ingestedWideUid
     *
     * @return string 
     */
    public function getIngestedWideUid()
    {
        return $this->ingestedWideUid;
    }

    /**
     * Set user
     *
     * @param \Ilios\CoreBundle\Model\User $user
     * @return ApiKey
     */
    public function setUser(\Ilios\CoreBundle\Model\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Ilios\CoreBundle\Model\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
