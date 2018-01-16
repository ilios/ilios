<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Service\PermissionChecker;
use Symfony\Component\Security\Core\Authorization\Voter\Voter as SymfonyVoter;

abstract class AbstractVoter extends SymfonyVoter
{
    /**
     * @var string
     */
    const VIEW = 'view';

    /**
     * @var string
     */
    const EDIT = 'edit';

    /**
     * @var string
     */
    const DELETE = 'delete';

    /**
     * @var string
     */
    const CREATE = 'create';

    /**
     * @var string
     */
    const UNLOCK = 'unlock';

    /**
     * @var string
     */
    const UNARCHIVE = 'unarchive';

    /**
     * @var bool
     */
    protected $abstain = false;

    /**
     * @var PermissionChecker
     */
    protected $permissionChecker;

    public function __construct(PermissionChecker $permissionChecker, bool $useNewPermissionsSystem = false)
    {
        $this->permissionChecker = $permissionChecker;
        $this->abstain = ! $useNewPermissionsSystem;
    }
}
