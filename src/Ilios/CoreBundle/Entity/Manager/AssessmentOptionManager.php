<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\AssessmentOptionInterface;

/**
 * Class AssessmentOptionManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class AssessmentOptionManager extends BaseManager implements AssessmentOptionManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findAssessmentOptionBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function deleteAssessmentOption(
        AssessmentOptionInterface $assessmentOption
    ) {
        $this->em->remove($assessmentOption);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createAssessmentOption()
    {
        $class = $this->getClass();
        return new $class();
    }
}
