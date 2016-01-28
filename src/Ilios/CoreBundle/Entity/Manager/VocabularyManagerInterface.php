<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\VocabularyInterface;

/**
 * Interface VocabularyManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface VocabularyManagerInterface extends ManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return VocabularyInterface
     */
    public function findVocabularyBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return VocabularyInterface[]
     */
    public function findVocabulariesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param VocabularyInterface $vocabulary
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateVocabulary(
        VocabularyInterface $vocabulary,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param VocabularyInterface $vocabulary
     *
     * @return void
     */
    public function deleteVocabulary(
        VocabularyInterface $vocabulary
    );

    /**
     * @return VocabularyInterface
     */
    public function createVocabulary();
}
