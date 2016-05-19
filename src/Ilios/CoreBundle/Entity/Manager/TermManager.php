<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\TermInterface;

/**
 * Class TermManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class TermManager extends BaseManager implements TermManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findTermBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * @inheritdoc
     */
    public function findTermDTOBy(
        array $criteria,
        array $orderBy = null
    ) {
        $results = $this->getRepository()->findDTOsBy($criteria, $orderBy, 1);
        return empty($results) ? false : $results[0];
    }

    /**
     * {@inheritdoc}
     */
    public function findTermsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function findTermDTOsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->getRepository()->findDTOsBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function updateTerm(
        TermInterface $term,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($term);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($term));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteTerm(
        TermInterface $term
    ) {
        $this->em->remove($term);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createTerm()
    {
        $class = $this->getClass();
        return new $class();
    }
}
