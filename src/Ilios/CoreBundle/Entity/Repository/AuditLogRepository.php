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
        $qb->select('a as log', 'u.id as userId')
            ->from('IliosCoreBundle:AuditLog', 'a')
            ->leftJoin('a.user', 'u')
            ->where(
                $qb->expr()->between(
                    'a.createdAt',
                    ':from',
                    ':to'
                )
            )
            ->setParameters(
                [
                    'from' => $from->format('Y-m-d H:i:s'),
                    'to' => $to->format('Y-m-d H:i:s:'),
                ]
            );

        $results = $qb->getQuery()->getArrayResult();
        $rhett = [];
        foreach ($results as $arr) {
            $combined = $arr['log'];
            $combined['userId'] = $arr['userId'];

            $rhett[] = $combined;
        }

        return $rhett;
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
                    'from' => $from->format('Y-m-d H:i:s'),
                    'to' => $to->format('Y-m-d H:i:s:'),
                ]
            );
        $qb->getQuery()->execute();
    }
}
