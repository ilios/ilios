<?php

namespace Ilios\CoreBundle\Service;

use Ilios\CoreBundle\Entity\Manager\AlertManager;
use Ilios\CoreBundle\Entity\Manager\BaseManager;
use Ilios\CoreBundle\Entity\Manager\UserManager;

/**
 * Creates and stores alerts for new or updated offerings.
 * Class ChangeAlertHandler
 * @package Ilios\CoreBundle\Service
 */
class ChangeAlertHandler
{
    /**
     * @var AlertManager
     */
    protected $alertManager;

    /**
     * @var BaseManager
     */
    protected $alertChangeTypeManager;

    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @param AlertManager $alertManager
     * @param BaseManager $alertChangeTypeManager
     * @param UserManager $userManager
     */
    public function __construct(
        AlertManager $alertManager,
        BaseManager $alertChangeTypeManager,
        UserManager $userManager
    ) {

    }
}
