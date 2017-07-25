<?php
namespace Ilios\CoreBundle\Service;

use Ilios\CoreBundle\Entity\AuditLog;
use Ilios\CoreBundle\Entity\Manager\AuditLogManager;
use Ilios\CoreBundle\Entity\Manager\BaseManager;
use Ilios\CoreBundle\Entity\UserInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;

/**
 * Class Logger
 */
class Logger
{
    /**
     * @var integer
     */
    protected $userId;

    /**
     * @var AuditLogManager
     */
    protected $manager;

    /**
     * @var LoggerInterface
     */
    protected $frameworkLogger;

    /**
     * @var array
     */
    protected $entries;

    /**
     * Set the userId from injected security context
     *
     * @param TokenStorageInterface $securityTokenStorage
     * @param AuditLogManager $auditLogManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        TokenStorageInterface $securityTokenStorage,
        AuditLogManager $auditLogManager,
        LoggerInterface $logger
    ) {
        if (null !== $securityTokenStorage &&
            null !== $securityTokenStorage->getToken()
        ) {
            /** @var SessionUserInterface $sessionUser */
            $sessionUser = $securityTokenStorage->getToken()->getUser();
            if ($sessionUser instanceof SessionUserInterface) {
                $this->userId = $sessionUser->getId();
            }
        }
        $this->manager = $auditLogManager;
        $this->frameworkLogger = $logger;
    }

    /**
     * Log an action
     *
     * @param $action
     * @param $objectId
     * @param $objectClass
     * @param $valuesChanged
     * @param bool $andFlush
     */
    public function log(
        $action,
        $objectId,
        $objectClass,
        $valuesChanged,
        $andFlush = true
    ) {
        if (!$this->userId) {
            throw new \Exception('Attempted to log something but there is no authenticated user.');
        }
        $log = [
            'action' => $action,
            'objectId' => $objectId,
            'objectClass' => $objectClass,
            'valuesChanged' => $valuesChanged,
            'userId' => $this->userId,
        ];
        $this->entries[] = $log;

        if ($andFlush) {
            $this->flush();
        }
    }

    /**
     * Write logs to the DB
     */
    public function flush()
    {
        try {
            $this->manager->writeLogs($this->entries);
            $this->entries = [];
        } catch (\Exception $e) {
            $this->frameworkLogger->alert('Unable to write logs: ' . $e->getMessage(), ['exception' => $e]);
        }
    }
}
