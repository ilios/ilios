<?php

namespace Ilios\CoreBundle\Model;

use Ilios\CoreBundle\Traits\IdentifiableTrait;

/**
 * Class IngestionException
 * @package Ilios\CoreBundle\Model
 */
class IngestionException implements IngestionExceptionInterface
{
    use IdentifiableTrait;

    /**
     * @var string
     */
    protected $ingestedWideUid;
    
    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @param string $ingestedWideUid
     */
    public function setIngestedWideUid($ingestedWideUid)
    {
        $this->ingestedWideUid = $ingestedWideUid;
    }

    /**
     * @return string
     */
    public function getIngestedWideUid()
    {
        return $this->ingestedWideUid;
    }

    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }
}
