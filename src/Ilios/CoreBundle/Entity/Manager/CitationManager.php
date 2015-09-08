<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\LearningMaterials\CitationInterface;

/**
 * Class CitationManager
 * @package Ilios\CoreBundle\Entity\Manager\LearningMaterials
 */
class CitationManager extends AbstractManager implements CitationManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CitationInterface
     */
    public function findCitationBy(
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
     * @return ArrayCollection|CitationInterface[]
     */
    public function findCitationsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param CitationInterface $citation
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateCitation(
        CitationInterface $citation,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($citation);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($citation));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param CitationInterface $citation
     */
    public function deleteCitation(
        CitationInterface $citation
    ) {
        $this->em->remove($citation);
        $this->em->flush();
    }

    /**
     * @return CitationInterface
     */
    public function createCitation()
    {
        $class = $this->getClass();
        return new $class();
    }
}
