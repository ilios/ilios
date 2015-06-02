<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\AssessmentOptionInterface;

/**
 * Class AssessmentOptionManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class AssessmentOptionManager implements AssessmentOptionManagerInterface
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
     * @param Registry $em
     * @param string $class
     */
    public function __construct(Registry $em, $class)
    {
        $this->em         = $em->getManagerForClass($class);
        $this->class      = $class;
        $this->repository = $em->getRepository($class);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return AssessmentOptionInterface
     */
    public function findAssessmentOptionBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|AssessmentOptionInterface[]
     */
    public function findAssessmentOptionsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param AssessmentOptionInterface $assessmentOption
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateAssessmentOption(
        AssessmentOptionInterface $assessmentOption,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($assessmentOption);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($assessmentOption));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param AssessmentOptionInterface $assessmentOption
     */
    public function deleteAssessmentOption(
        AssessmentOptionInterface $assessmentOption
    ) {
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

    /**
     * @return AssessmentOptionInterface
     */
    public function createAssessmentOption()
    {
        $class = $this->getClass();
        return new $class();
    }
}
