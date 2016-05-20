<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\VocabularyInterface;

/**
 * Class VocabularyManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class VocabularyManager extends BaseManager
{
    /**
     * @deprecated
     */
    public function findVocabularyBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findVocabulariesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @deprecated
     */
    public function updateVocabulary(
        VocabularyInterface $vocabulary,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($vocabulary, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteVocabulary(
        VocabularyInterface $vocabulary
    ) {
        $this->delete($vocabulary);
    }

    /**
     * @deprecated
     */
    public function createVocabulary()
    {
        return $this->create();
    }
}
