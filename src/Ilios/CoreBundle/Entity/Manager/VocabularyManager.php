<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\VocabularyInterface;

/**
 * Class VocabularyManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class VocabularyManager extends BaseManager implements VocabularyManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findVocabularyBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findVocabulariesBy(
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
    public function updateVocabulary(
        VocabularyInterface $vocabulary,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($vocabulary);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($vocabulary));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteVocabulary(
        VocabularyInterface $vocabulary
    ) {
        $this->em->remove($vocabulary);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createVocabulary()
    {
        $class = $this->getClass();
        return new $class();
    }
}
