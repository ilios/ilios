<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class AuditLogRepository
 * @package Ilios\CoreBundle\Entity\Repository
 */
class AuditLogRepository extends EntityRepository
{
    /**
     * Returns all audit log entries in a given date/time range.
     *
     * @param \DateTime $from
     * @param \DateTime $to
     * @return array
     */
    public function findInRange(\DateTime $from, \DateTime $to)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->add('select', 'a')
            ->from('IliosCoreBundle:AuditLog', 'a')
            ->add(
                'where',
                $qb->expr()->between(
                    'a.createdAt',
                    ':from',
                    ':to'
                )
            )
            ->setParameters(
                [
                    'from' => $from->format('c'),
                    'to' => $to->format('c'),
                ]
            );

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * Deletes all audit log entries in a given date/time range.
     *
     * @param \DateTime $from
     * @param \DateTime $to
     */
    public function deleteInRange(\DateTime $from, \DateTime $to)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb
            ->delete('IliosCoreBundle:AuditLog', 'a')
            ->add(
                'where',
                $qb->expr()->between(
                    'a.createdAt',
                    ':from',
                    ':to'
                )
            )
            ->setParameters(
                [
                    'from' => $from->format('c'),
                    'to' => $to->format('c'),
                ]
            );
        $qb->getQuery()->execute();
    }
}
