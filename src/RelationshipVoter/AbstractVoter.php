<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Service\PermissionChecker;
use Symfony\Component\Security\Core\Authorization\Voter\Voter as SymfonyVoter;

abstract class AbstractVoter extends SymfonyVoter
{
    /**
     * @var string
     */
    public const VIEW = 'view';

    /**
     * @var string
     */
    public const EDIT = 'edit';

    /**
     * @var string
     */
    public const DELETE = 'delete';

    /**
     * @var string
     */
    public const CREATE = 'create';

    /**
     * @var string
     */
    public const UNLOCK = 'unlock';

    /**
     * @var string
     */
    public const LOCK = 'lock';

    /**
     * @var string
     */
    public const ARCHIVE = 'archive';

    /**
     * @var string
     */
    public const ROLLOVER = 'rollover';

    /**
     * @param PermissionChecker $permissionChecker
     */
    public function __construct(protected PermissionChecker $permissionChecker)
    {
    }
}
