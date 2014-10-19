<?php

namespace Ilios\CoreBundle\Model;

use Ilios\CoreBundle\Traits\IdentifiableTraitIntertface;

/**
 * Interface IngestionExceptionInterface
 * @package Ilios\CoreBundle\Model
 */
interface IngestionExceptionInterface extends IdentifiableTraitIntertface
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

