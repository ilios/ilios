<?php

declare(strict_types=1);

namespace App\Entity\Manager;

use App\Entity\DTO\LearningMaterialDTO;
use App\Entity\LearningMaterialInterface;
use App\Repository\LearningMaterialRepository;

/**
 * Class LearningMaterialManager
 */
class LearningMaterialManager extends BaseManager
{
    /**
     * Use a query term to find learning materials
     *
     * @return LearningMaterialDTO[]
     */
    public function findLearningMaterialDTOsByQ(string $q, ?array $orderBy, ?int $limit, ?int $offset): array
    {
        /** @var LearningMaterialRepository $repository */
        $repository = $this->getRepository();
        return $repository->findDTOsByQ($q, $orderBy, $limit, $offset);
    }

    /**
     * Find all the File type learning materials
     * @param int $limit
     * @param int $offset
     *
     * @return LearningMaterialInterface[]
     */
    public function findFileLearningMaterials($limit, $offset)
    {
        /** @var LearningMaterialRepository $repository */
        $repository = $this->getRepository();
        return $repository->findFileLearningMaterials($limit, $offset);
    }

    /**
     * @return int
     */
    public function getTotalFileLearningMaterialCount()
    {
        $dql = 'SELECT COUNT(l.id) FROM App\Entity\LearningMaterial l WHERE l.relativePath IS NOT NULL';
        return $this->em
            ->createQuery($dql)->getSingleScalarResult();
    }

    /**
     * @return int
     */
    public function getTotalLearningMaterialCount(): int
    {
        return (int) $this->em
            ->createQuery('SELECT COUNT(l.id) FROM App\Entity\LearningMaterial l')->getSingleScalarResult();
    }

    /**
     * Get all the IDs for learning materials that are files
     * int[]
     */
    public function getFileLearningMaterialIds(): array
    {
        $dql = 'SELECT l.id FROM App\Entity\LearningMaterial l WHERE l.relativePath IS NOT NULL';
        $results = $this->em->createQuery($dql)->getScalarResult();
        $ids = array_column($results, 'id');
        return array_map('intval', $ids);
    }
}
