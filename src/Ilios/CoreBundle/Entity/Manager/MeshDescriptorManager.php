<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Service\MeshDescriptorSetTransmogrifier;
use Ilios\CoreBundle\Entity\MeshDescriptorInterface;
use Ilios\CoreBundle\Entity\Repository\MeshDescriptorRepository;
use Ilios\MeSH\Model\DescriptorSet;

/**
 * Class MeshDescriptorManager
 */
class MeshDescriptorManager extends BaseManager
{
    /**
     * @var MeshDescriptorSetTransmogrifier $transmogrifier
     */
    protected $transmogrifier;

    /**
     * @param Registry $registry
     * @param string $class
     * @param MeshDescriptorSetTransmogrifier $transmogrifier
     */
    public function __construct(Registry $registry, $class, MeshDescriptorSetTransmogrifier $transmogrifier)
    {
        $this->transmogrifier = $transmogrifier;
        parent::__construct($registry, $class);
    }
    /**
     * @param string $q
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return MeshDescriptorInterface[]
     */
    public function findMeshDescriptorsByQ(
        $q,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->getRepository()->findByQ($q, $orderBy, $limit, $offset);
    }

    /**
     * Single entry point for importing a given MeSH record into its corresponding database table.
     *
     * @param array $data An associative array containing a MeSH record.
     * @param string $type The type of MeSH data that's being imported.
     * @throws \Exception on unsupported type.
     */
    public function import(array $data, $type)
    {
        // KLUDGE!
        // For performance reasons, we're completely side-stepping
        // Doctrine's entity layer.
        // Instead, this method invokes low-level/native-SQL import-methods
        // on this manager's repository.
        // [ST 2015/09/08]
        /**
         * @var MeshDescriptorRepository $repository
         */
        $repository = $this->getRepository();
        switch ($type) {
            case 'MeshDescriptor':
                $repository->importMeshDescriptor($data);
                break;
            case 'MeshTree':
                $repository->importMeshTree($data);
                break;
            case 'MeshConcept':
                $repository->importMeshConcept($data);
                break;
            case 'MeshTerm':
                $repository->importMeshTerm($data);
                break;
            case 'MeshQualifier':
                $repository->importMeshQualifier($data);
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
                throw new \Exception("Unsupported type ${type}.");
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
}
