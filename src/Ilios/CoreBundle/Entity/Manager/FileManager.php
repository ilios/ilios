<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\LearningMaterials\FileInterface;

/**
 * Class FileManager
 * @package Ilios\CoreBundle\Entity\Manager\LearningMaterials
 */
class FileManager extends AbstractManager implements FileManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return FileInterface
     */
    public function findFileBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|FileInterface[]
     */
    public function findFilesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param FileInterface $file
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateFile(
        FileInterface $file,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($file);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($file));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param FileInterface $file
     */
    public function deleteFile(
        FileInterface $file
    ) {
        $this->em->remove($file);
        $this->em->flush();
    }

    /**
     * @return FileInterface
     */
    public function createFile()
    {
        $class = $this->getClass();
        return new $class();
    }
}
