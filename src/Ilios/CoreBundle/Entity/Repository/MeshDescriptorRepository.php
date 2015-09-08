<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class MeshDescriptorRepository
 * @package Ilios\CoreBundle\Entity\Repository
 */
class MeshDescriptorRepository extends EntityRepository
{
    /**
     * Find by a string query.
     *
     * @param string $q
     * @param integer $orderBy
     * @param integer $limit
     * @param integer $offset
     * @return array
     */
    public function findByQ($q, $orderBy, $limit, $offset)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->add('select', 'd')->from('MeshDescriptor', 'd');
        $qb->leftJoin('d.previousIndexing', 'pi');
        $qb->leftJoin('d.concepts', 'c');
        $qb->leftJoin('c.semanticTypes', 'st');
        $qb->leftJoin('c.terms', 't');
        $terms = explode(' ', $q);
        $terms = array_filter($terms, 'strlen');
        if (empty($terms)) {
            return new ArrayCollection([]);
        }

        foreach ($terms as $key => $term) {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('d.id', "?{$key}"),
                $qb->expr()->like('d.name', "?{$key}"),
                $qb->expr()->like('d.annotation', "?{$key}"),
                $qb->expr()->like('pi.previousIndexing', "?{$key}"),
                $qb->expr()->like('st.name', "?{$key}"),
                $qb->expr()->like('t.name', "?{$key}"),
                $qb->expr()->like('c.name', "?{$key}"),
                $qb->expr()->like('c.scopeNote', "?{$key}"),
                $qb->expr()->like('c.casn1Name', "?{$key}")
            ))
            ->setParameter($key, '%' . $term . '%');
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('d.' . $sort, $order);
            }
        }

        if ($offset) {
            $qb->setFirstResult($offset);
        }
        $query = $qb->getQuery();
        $query->useResultCache(true);
        
        $results = $query->getResult();
        
        // Unfortunately, we can't let Doctrine limit the fetch here because of all the joins
        // it returns many less than the desired number.
        if ($limit) {
            $results = array_slice($results, 0, $limit);
        }

        return $results;
    }

    /**
     * @param array $data
     */
    public function importMeshConcept(array $data)
    {
        $connection = $this->_em->getConnection();
        $sql =<<<EOL
INSERT INTO mesh_concept (
    mesh_concept_uid, name, umls_uid, preferred, scope_note,
    casn_1_name, registry_number, created_at, updated_at
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
EOL;
        $connection->executeUpdate($sql, $data);
    }

    /**
     * @param array $data
     */
    public function importMeshConceptSemanticType(array $data)
    {
        $sql =<<<EOL
INSERT INTO mesh_concept_x_semantic_type (
    mesh_concept_uid, mesh_semantic_type_uid
) VALUES (?, ?)
EOL;
        $connection = $this->_em->getConnection();
        $connection->executeUpdate($sql, $data);
    }

    /**
     * @param array $data
     */
    public function importMeshConceptTerm(array $data)
    {
        $sql =<<<EOL
INSERT INTO mesh_concept_x_term (
    mesh_concept_uid, mesh_term_id
) VALUES (?, ?)
EOL;
        $connection = $this->_em->getConnection();
        $connection->executeUpdate($sql, $data);
    }

    /**
     * @param array $data
     */
    public function importMeshDescriptor(array $data)
    {
        $sql =<<<EOL
INSERT INTO mesh_descriptor (
    mesh_descriptor_uid, name, annotation, created_at, updated_at
) VALUES (?, ?, ?, ?, ?)
EOL;
        $connection = $this->_em->getConnection();
        $connection->executeUpdate($sql, $data);
    }

    /**
     * @param array $data
     */
    public function importMeshDescriptorConcept(array $data)
    {
        {
            $sql =<<<EOL
INSERT INTO mesh_descriptor_x_concept (
    mesh_concept_uid, mesh_descriptor_uid
) VALUES (?, ?)
EOL;
            $connection = $this->_em->getConnection();
            $connection->executeUpdate($sql, $data);
        }
    }

    /**
     * @param array $data
     */
    public function importMeshDescriptorQualifier(array $data)
    {
        $sql =<<<EOL
INSERT INTO mesh_descriptor_x_qualifier (
    mesh_descriptor_uid, mesh_qualifier_uid
) VALUES (?, ?)
EOL;
        $connection = $this->_em->getConnection();
        $connection->executeUpdate($sql, $data);
    }

    /**
     * @param array $data
     */
    public function importMeshPreviousIndexing(array $data)
    {
        $sql =<<<EOL
INSERT INTO mesh_previous_indexing (
    mesh_descriptor_uid, previous_indexing, mesh_previous_indexing_id
) VALUES (?, ?, ?)
EOL;
        $connection = $this->_em->getConnection();
        $connection->executeUpdate($sql, $data);
    }

    /**
     * @param array $data
     */
    public function importMeshQualifier(array $data)
    {

        $sql =<<<EOL
INSERT INTO mesh_qualifier (
    mesh_qualifier_uid, name, created_at, updated_at
) VALUES (?, ?, ?, ?)
EOL;
        $connection = $this->_em->getConnection();
        $connection->executeUpdate($sql, $data);
    }

    /**
     * @param array $data
     */
    public function importMeshSemanticType(array $data)
    {
        $sql =<<<EOL
INSERT INTO mesh_semantic_type (
    mesh_semantic_type_uid, name, created_at, updated_at
) VALUES (?, ?, ?, ?)
EOL;
        $connection = $this->_em->getConnection();
        $connection->executeUpdate($sql, $data);
    }

    /**
     * @param array $data
     */
    public function importMeshTerm(array $data)
    {
        $sql =<<<EOL
INSERT INTO mesh_term (
    mesh_term_uid, name, lexical_tag, concept_preferred, record_preferred, permuted,
    print, created_at, updated_at, mesh_term_id
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
EOL;
        $connection = $this->_em->getConnection();
        $connection->executeUpdate($sql, $data);
    }

    /**
     * @param array $data
     */
    public function importMeshTree(array $data)
    {
        $sql =<<<EOL
INSERT INTO mesh_tree_x_descriptor (
    tree_number, mesh_descriptor_uid, mesh_tree_id
) VALUES (?, ?, ?)
EOL;
        $connection = $this->_em->getConnection();
        $connection->executeUpdate($sql, $data);
    }
}
