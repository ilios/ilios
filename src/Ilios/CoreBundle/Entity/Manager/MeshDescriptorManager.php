<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\MeshDescriptorInterface;
use Ilios\CoreBundle\Entity\Repository\MeshDescriptorRepository;
use Ilios\MeSH\Model\DescriptorSet;

/**
 * Class MeshDescriptorManager
 */
class MeshDescriptorManager extends BaseManager
{
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
            case 'MeshSemanticType':
                $repository->importMeshSemanticType($data);
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
            case 'MeshConceptSemanticType':
                $repository->importMeshConceptSemanticType($data);
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
        $data = $this->transmogrifyMeSHDataForImport($descriptorSet, $existingDescriptorIds);
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
     * @param DescriptorSet $descriptors
     * @param string[] $existingDescriptorIds
     * @return array
     */
    private function transmogrifyMeSHDataForImport(DescriptorSet $descriptors, array $existingDescriptorIds)
    {
        $rhett = [
            'insert' => [
                'mesh_concept' => [],
                'mesh_concept_x_term' => [],
                'mesh_descriptor' => [],
                'mesh_descriptor_x_concept' => [],
                'mesh_descriptor_x_qualifier' => [],
                'mesh_qualifier' => [],
                'mesh_previous_indexing' => [],
                'mesh_term' => [],
                'mesh_tree' => [],
            ],
            'update' => [
                'mesh_descriptor' => [],
            ],
        ];

        foreach ($descriptors->getDescriptors() as $descriptor) {
            if (in_array($descriptor->getUi(), $existingDescriptorIds)) {
                $rhett['update']['mesh_descriptor'][$descriptor->getUi()] = $descriptor;
            } else {
                $rhett['insert']['mesh_descriptor'][$descriptor->getUi()] = $descriptor;
            }
            foreach ($descriptor->getConcepts() as $concept) {
                $rhett['insert']['mesh_concept'][$concept->getUi()] = $concept;
                $rhett['insert']['mesh_descriptor_x_concept'][] = [$descriptor->getUi(), $concept->getUi()];
                foreach ($concept->getTerms() as $term) {
                    // ACHTUNG MINEN!
                    // Unlike all other MeSH data points, terms do *not* possess unique UID.
                    // Generate a unique pseudo-key by hashing all relevant term properties,
                    // Use this hash instead of UID to keep track of term relationships
                    // and all relevant term permutations.
                    // [ST 2017/09/07]
                    $hash = md5(
                        implode(
                            ',',
                            [
                                $term->getUi(),
                                $term->getName(),
                                $term->getLexicalTag(),
                                $term->isConceptPreferred(),
                                $term->isRecordPreferred(),
                                $term->isPermuted(),
                            ]
                        )
                    );
                    $rhett['insert']['mesh_term'][$hash] = $term;
                    $rhett['insert']['mesh_concept_x_term'][] = [$concept->getUi(), $hash];
                }
            }
            $rhett['insert']['mesh_tree'][$descriptor->getUi()] = $descriptor->getTreeNumbers();
            $prevIndexing = $descriptor->getPreviousIndexing();
            if (!empty($prevIndexing)) {
                // KNOWN ISSUE
                // despite the one-to-many relationship of descriptors to their previous indexing records,
                // we currently treat this relationship as a one-to-one, only taking
                // the last previous indexing record into account.
                // @todo revisit and fix. [ST 2017/09/08]
                $rhett['insert']['mesh_previous_indexing'][$descriptor->getUi()] = array_reverse($prevIndexing)[0];
            }
            foreach ($descriptor->getAllowableQualifiers() as $qualifier) {
                $rhett['insert']['mesh_qualifier'][$qualifier->getQualifierReference()->getUi()] = $qualifier;
                $rhett['insert']['mesh_descriptor_x_qualifier'][] = [
                    $descriptor->getUi(),
                    $qualifier->getQualifierReference()->getUi(),
                ];
            }
        }

        return $rhett;
    }
}
