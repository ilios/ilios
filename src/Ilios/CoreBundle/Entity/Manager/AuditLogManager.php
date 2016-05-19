<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\AuditLogInterface;

/**
 * Class AuditLogManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class AuditLogManager extends BaseManager implements AuditLogManagerInterface
{

    /**
     * {@inheritdoc}
     */
    public function findAuditLogBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findAuditLogsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function updateAuditLog(
        AuditLogInterface $auditLog,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($auditLog);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($auditLog));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAuditLog(
        AuditLogInterface $auditLog
    ) {
        $this->em->remove($auditLog);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createAuditLog()
    {
        $class = $this->getClass();
        return new $class();
    }

    /**
     * {@inheritdoc}
     */
    public function findInRange(\DateTime $from, \DateTime $to)
    {
        return $this->getRepository()->findInRange($from, $to);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteInRange(\Datetime $from, \DateTime $to)
    {
        $this->getRepository()->deleteInRange($from, $to);
    }


    /**
     * {@inheritdoc}
     */
    public function getFieldNames()
    {
        return $this->em->getClassMetadata($this->getClass())->getFieldNames();
    }
}
