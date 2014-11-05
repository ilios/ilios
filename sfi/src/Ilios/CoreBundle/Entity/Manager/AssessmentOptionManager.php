<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\AssessmentOptionManager as BaseAssessmentOptionManager;
use Ilios\CoreBundle\Model\AssessmentOptionInterface;

class AssessmentOptionManager extends BaseAssessmentOptionManager
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
     * @return AssessmentOptionInterface
     */
    public function findAssessmentOptionBy(array $criteria, array $orderBy = null)
    {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * Previously known as findAllBy()
     *
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return AssessmentOptionInterface[]|Collection
     */
    public function findAssessmentOptionsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param AssessmentOptionInterface $assessmentOption
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateAssessmentOption(AssessmentOptionInterface $assessmentOption, $andFlush = true)
    {
        $this->em->persist($assessmentOption);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param AssessmentOptionInterface $assessmentOption
     *
     * @return void
     */
    public function deleteAssessmentOption(AssessmentOptionInterface $assessmentOption)
    {
        $this->em->remove($assessmentOption);
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
