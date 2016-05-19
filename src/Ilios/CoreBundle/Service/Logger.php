<?php
namespace Ilios\CoreBundle\Service;

use Ilios\CoreBundle\Entity\Manager\AuditLogManager;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class Logger
 * @package Ilios\CoreBundle\Service
 */
class Logger
{
    /**
     * @var UserInterface
     */
    protected $user;
    
    /**
     * @var AuditLogManager
     */
    protected $manager;

    /**
     * Set the username from injected security context
     * @param TokenStorageInterface $securityTokenStorage
     * @param AuditLogManager $auditLogManager
     */
    public function __construct(
        TokenStorageInterface $securityTokenStorage,
        AuditLogManager $auditLogManager
    ) {
        if (null !== $securityTokenStorage &&
            null !== $securityTokenStorage->getToken()
        ) {
            $this->user = $securityTokenStorage->getToken()->getUser();
        }
        $this->manager = $auditLogManager;
    }
    
    public function log(
        $action,
        $objectId,
        $objectClass,
        $valuesChanged,
        $andFlush = true
    ) {
        $log = $this->manager->createAuditLog();
        $log->setAction($action);
        $log->setObjectId($objectId);
        $log->setObjectClass($objectClass);
        $log->setValuesChanged($valuesChanged);
        $log->setUser($this->user);

        $this->manager->updateAuditLog($log, $andFlush);
        
        return $log;
    }
}
