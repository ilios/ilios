<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\AuditAtomInterface;

/**
 * Interface AuditAtomManagerInterface
 */
interface AuditAtomManagerInterface
{
    /** 
     *@return AuditAtomInterface
     */
    public function createAuditAtom();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return AuditAtomInterface
     */
    public function findAuditAtomBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return AuditAtomInterface[]|Collection
     */
    public function findAuditAtomsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param AuditAtomInterface $auditAtom
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateAuditAtom(AuditAtomInterface $auditAtom, $andFlush = true);

    /**
     * @param AuditAtomInterface $auditAtom
     *
     * @return void
     */
    public function deleteAuditAtom(AuditAtomInterface $auditAtom);

    /**
     * @return string
     */
    public function getClass();
}
