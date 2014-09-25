<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\AuditAtomManager as BaseAuditAtomManager;
use Ilios\CoreBundle\Model\AuditAtomInterface;

class AuditAtomManager extends BaseAuditAtomManager
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $class;

    /**
     * @param EntityManager $em
     * @param string $class
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em         = $em;
        $this->class      = $class;
        $this->repository = $em->getRepository($class);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return AuditAtomInterface
     */
    public function findAuditAtomBy(array $criteria, array $orderBy = null)
    {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * Previously known as findAllBy()
     *
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return AuditAtomInterface[]|Collection
     */
    public function findAuditAtomsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param AuditAtomInterface $auditAtom
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateAuditAtom(AuditAtomInterface $auditAtom, $andFlush = true)
    {
        $this->em->persist($auditAtom);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param AuditAtomInterface $auditAtom
     *
     * @return void
     */
    public function deleteAuditAtom(AuditAtomInterface $auditAtom)
    {
        $this->em->remove($auditAtom);
        $this->em->flush();
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }
}
