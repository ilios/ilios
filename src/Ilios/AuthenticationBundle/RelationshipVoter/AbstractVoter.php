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
     * @var PermissionChecker
     */
    protected $permissionChecker;

    /**
     * @param PermissionChecker $permissionChecker
     */
    public function __construct(PermissionChecker $permissionChecker)
    {
        $this->permissionChecker = $permissionChecker;
    }
}
