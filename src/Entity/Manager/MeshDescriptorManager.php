<?php

declare(strict_types=1);

namespace App\Entity\Manager;

use App\Entity\DTO\MeshDescriptorDTO;
use App\Service\MeshDescriptorSetTransmogrifier;
use App\Repository\MeshDescriptorRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Ilios\MeSH\Model\Descriptor;
use Ilios\MeSH\Model\DescriptorSet;

/**
 * Class MeshDescriptorManager
 */
class MeshDescriptorManager extends V1CompatibleBaseManager
{
    /**
     * @var MeshDescriptorSetTransmogrifier $transmogrifier
     */
    protected $transmogrifier;

    /**
     * @param ManagerRegistry $registry
     * @param string $class
     * @param MeshDescriptorSetTransmogrifier $transmogrifier
     */
    public function __construct(
        ManagerRegistry $registry,
        $class,
        MeshDescriptorSetTransmogrifier $transmogrifier
    ) {
        parent::__construct($registry, $class);
        $this->transmogrifier = $transmogrifier;
    }

    /**
     * @param string $q
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     * @throws Exception
     */
    public function findMeshDescriptorDTOsByQ(
        string $q,
        ?array $orderBy,
        ?int $limit,
        ?int $offset
    ): array {
        /** @var MeshDescriptorRepository $repository */
        $repository = $this->getRepository();
        return $repository->findDTOsByQ($q, $orderBy, $limit, $offset);
    }

    /**
     * @param string $q
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     * @throws Exception
     */
    public function findMeshDescriptorV1DTOsByQ(
        string $q,
        ?array $orderBy,
        ?int $limit,
        ?int $offset
    ): array {
        /** @var MeshDescriptorRepository $repository */
        $repository = $this->getRepository();
        return $repository->findV1DTOsByQ($q, $orderBy, $limit, $offset);
    }

    /**
     * Single entry point for importing a given MeSH record into its corresponding database table.
     *
     * @param array $data An associative array containing a MeSH record.
     * @param string $type The type of MeSH data that's being imported.
     * @param string|null $now The current time and date as an ANSI SQL compatible string representation.
     * @throws Exception on unsupported type.
     */
    public function import(array $data, $type, string $now = null)
    {
        /**
         * @var MeshDescriptorRepository $repository
         */
        $repository = $this->getRepository();
        switch ($type) {
            case 'MeshDescriptor':
                $repository->importMeshDescriptor($data, $now);
                break;
            case 'MeshTree':
                $repository->importMeshTree($data);
                break;
            case 'MeshConcept':
                $repository->importMeshConcept($data, $now);
                break;
            case 'MeshTerm':
                $repository->importMeshTerm($data, $now);
                break;
            case 'MeshQualifier':
                $repository->importMeshQualifier($data, $now);
                break;
            case 'MeshPreviousIndexing':
                $repository->importMeshPreviousIndexing($data);
                break;
            case 'MeshConceptTerm':
                $repository->importMeshConceptTerm($data);
                break;
            case 'MeshDescriptorQualifier':
                $repository->importMeshDescriptorQualifier($data);
                break;
            case 'MeshDescriptorConcept':
                $repository->importMeshDescriptorConcept($data);
                break;
            default:
                throw new Exception("Unsupported type ${type}.");
        }
    }

    /**
     * @see MeshDescriptorRepository::clearExistingData()
     */
    public function clearExistingData()
    {
        $this->getRepository()->clearExistingData();
    }

    /**
     * @param DescriptorSet $descriptorSet
     * @param array $existingDescriptorIds
     * @see MeshDescriptorRepository::upsertMeshUniverse()
     */
    public function upsertMeshUniverse(DescriptorSet $descriptorSet, array $existingDescriptorIds)
    {
        $data = $this->transmogrifier->transmogrify($descriptorSet, $existingDescriptorIds);
        $this->getRepository()->upsertMeshUniverse($data);
    }

    /**
     * @param array $meshDescriptors
     * @see MeshDescriptorRepository::flagDescriptorsAsDeleted()
     */
    public function flagDescriptorsAsDeleted(array $meshDescriptors)
    {
        $this->getRepository()->flagDescriptorsAsDeleted($meshDescriptors);
    }

    /**
     * Get all the IDs for every descriptor
     *
     * @return array
     * @throws Exception
     */
    public function getIds(): array
    {
        /** @var MeshDescriptorRepository $repository */
        $repository = $this->getRepository();
        return $repository->getIds();
    }

    /**
     * Get Descriptors
     *
     * @param array $ids
     * @return Descriptor[]
     * @throws Exception
     */
    public function getIliosMeshDescriptorsById(array $ids): array
    {
        /** @var MeshDescriptorRepository $repository */
        $repository = $this->getRepository();
        return $repository->getIliosMeshDescriptorsById($ids);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function exportMeshDescriptors(): array
    {
        return $this->getRepository()->exportMeshDescriptors();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function exportMeshTrees(): array
    {
        return $this->getRepository()->exportMeshTrees();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function exportMeshConcepts(): array
    {
        return $this->getRepository()->exportMeshConcepts();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function exportMeshTerms(): array
    {
        return $this->getRepository()->exportMeshTerms();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function exportMeshQualifiers(): array
    {
        return $this->getRepository()->exportMeshQualifiers();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function exportMeshPreviousIndexings(): array
    {
        return $this->getRepository()->exportMeshPreviousIndexings();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function exportMeshConceptTerms(): array
    {
        return $this->getRepository()->exportMeshConceptTerms();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function exportMeshDescriptorQualifiers(): array
    {
        return $this->getRepository()->exportMeshDescriptorQualifiers();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function exportMeshDescriptorConcepts(): array
    {
        return $this->getRepository()->exportMeshDescriptorConcepts();
    }
}
