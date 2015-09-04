<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\AuditLogInterface;
use Ilios\CoreBundle\Entity\Repository\AuditLogRepository;

/**
 * Class AuditLogManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class AuditLogManager extends AbstractManager implements AuditLogManagerInterface
{

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return AuditLogInterface
     */
    public function findAuditLogBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|AuditLogInterface[]
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
     * @param AuditLogInterface $auditLog
     * @param bool $andFlush
     * @param bool $forceId
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
     * @param AuditLogInterface $auditLog
     */
    public function deleteAuditLog(
        AuditLogInterface $auditLog
    ) {
        $this->em->remove($auditLog);
        $this->em->flush();
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return AuditLogInterface
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
     * Returns a list of field names of the corresponding entity.
     *
     * @return array
     *
     * @todo Refactor this out into a trait or stick it somewhere else. [ST 2015/09/02]
     */
    public function getFieldNames()
    {
        return $this->em->getClassMetadata($this->getClass())->getFieldNames();
    }
}
