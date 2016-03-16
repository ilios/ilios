<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\TermInterface;
use Ilios\CoreBundle\Entity\DTO\TermDTO;

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
     *
     * @return TermDTO|bool a session object or FALSE if none were found.
     */
    public function findTermDTOBy(
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
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return TermDTO[]
     */
    public function findTermDTOsBy(
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
