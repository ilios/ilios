<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\TermInterface;

/**
 * Class TermManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class TermManager extends DTOManager
{
    /**
     * @deprecated
     */
    public function findTermBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findTermDTOBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findDTOBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findTermsBy(
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
    public function findTermDTOsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->findDTOsBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @deprecated
     */
    public function updateTerm(
        TermInterface $term,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($term, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteTerm(
        TermInterface $term
    ) {
        $this->delete($term);
    }

    /**
     * @deprecated
     */
    public function createTerm()
    {
        return $this->create();
    }
}
