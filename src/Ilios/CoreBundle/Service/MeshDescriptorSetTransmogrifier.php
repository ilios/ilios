<?php

namespace Ilios\CoreBundle\Service;

use Ilios\MeSH\Model\DescriptorSet;
use Ilios\MeSH\Model\Term;

/**
 * Class MeshDescriptorSetTransmogrifier
 * @package Ilios\CoreBundle\Classes
 */
class MeshDescriptorSetTransmogrifier
{
    /**
     * Transforms a given MeSH descriptors set into a nested array structure as a pre-processing step for
     * database updates/insertions.
     *
     * @param DescriptorSet $descriptors
     * @param array $existingDescriptorIds
     * @return array
     */
    public function transmogrify(DescriptorSet $descriptors, array $existingDescriptorIds)
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
                    $hash = $this->hashTerm($term);
                    $rhett['insert']['mesh_term'][$hash] = $term;
                    $rhett['insert']['mesh_concept_x_term'][] = [$concept->getUi(), $hash];
                }
            }
            $treeNumbers = $descriptor->getTreeNumbers();
            if (!empty($treeNumbers)) {
                $rhett['insert']['mesh_tree'][$descriptor->getUi()] = $treeNumbers;
            }
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

    /**
     * Creates a MD5 hash from all relevant properties of a given MeSH term.
     * @param Term $term
     * @return string
     */
    public function hashTerm(Term $term)
    {
        return md5(
            implode(
                '||',
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
    }
}
