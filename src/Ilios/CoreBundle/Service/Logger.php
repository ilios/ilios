<?php
namespace Ilios\CoreBundle\Service;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use Ilios\CoreBundle\Entity\UserInterface;
use Ilios\CoreBundle\Entity\Manager\AuditLogManagerInterface;

class Logger
{
    /**
     * @var UserInterface
     */
    protected $user;
    
    /**
     * @var AuditLogManagerInterface
     */
    protected $manager;

    /**
     * Set the username from injected security context
     * @param TokenStorageInterface $securityTokenStorage
     * @param AuditLogManagerInterface $auditLogManager
     */
    public function __construct(
        TokenStorageInterface $securityTokenStorage,
        AuditLogManagerInterface $auditLogManager
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
