<?php

namespace Ilios\CoreBundle\Model;

/**
 * Interface IngestionExceptionInterface
 * @package Ilios\CoreBundle\Model
 */
interface IngestionExceptionInterface
{
    /**
     * @param string $ingestedWideUid
     */
    public function setIngestedWideUid($ingestedWideUid);

    /**
     * @return string
     */
    public function getIngestedWideUid();

    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user);

    /**
     * @return UserInterface
     */
    public function getUser();
}

