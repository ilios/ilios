<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\TermInterface;

/**
 * Interface TermManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface TermManagerInterface extends ManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return TermInterface
     */
    public function findTermBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return TermInterface[]
     */
    public function findTermsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param TermInterface $term
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateTerm(
        TermInterface $term,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param TermInterface $term
     *
     * @return void
     */
    public function deleteTerm(
        TermInterface $term
    );

    /**
     * @return TermInterface
     */
    public function createTerm();
}
