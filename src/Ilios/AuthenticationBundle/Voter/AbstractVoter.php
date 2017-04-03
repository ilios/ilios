<?php
namespace Ilios\AuthenticationBundle\Voter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\SchoolInterface;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class AbstractVoter
 * @package Ilios\AuthenticationBundle\Voter
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
}
