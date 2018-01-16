<?php
namespace Ilios\AuthenticationBundle\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class AbstractVoter
 */
abstract class AbstractVoter extends Voter
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
     * @var bool
     */
    protected $abstain = false;

    public function __construct(bool $useNewPermissionsSystem = false)
    {
        $this->abstain = $useNewPermissionsSystem;
    }
}
