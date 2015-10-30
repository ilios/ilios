<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\ReportInterface;
use Ilios\CoreBundle\Entity\Manager\PermissionManagerInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class ReportVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class ReportVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\ReportInterface');
    }

    /**
     * @param string $attribute
     * @param ReportInterface $report
     * @param UserInterface $user
     * @return bool
     */
    protected function isGranted($attribute, $report, $user = null)
    {
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            // Users can perform any CRUD operations on their own reports.
            // Check if the given report's owning user is the given user.
            case self::CREATE:
            case self::VIEW:
            case self::EDIT:
            case self::DELETE:
                return $this->usersAreIdentical($user, $report->getUser());
                break;
        }

        return false;
    }
}
