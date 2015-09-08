<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\AssessmentOptionInterface;

/**
 * Class AssessmentOptionManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class AssessmentOptionManager extends AbstractManager implements AssessmentOptionManagerInterface
{
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
        return $this->getRepository()->findOneBy($criteria, $orderBy);
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
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
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
     * @return AssessmentOptionInterface
     */
    public function createAssessmentOption()
    {
        $class = $this->getClass();
        return new $class();
    }
}
