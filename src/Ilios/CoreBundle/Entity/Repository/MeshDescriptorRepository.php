<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Entity\MeshDescriptorInterface;

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
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     * @return MeshDescriptorInterface[]
     */
    public function findByQ($q, array $orderBy, $limit, $offset)
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('DISTINCT d')
            ->from('IliosCoreBundle:MeshDescriptor', 'd')
            ->leftJoin('d.previousIndexing', 'pi')
            ->leftJoin('d.concepts', 'c')
            ->leftJoin('c.semanticTypes', 'st')
            ->leftJoin('c.terms', 't');

        $terms = explode(' ', $q);
        $terms = array_filter($terms, 'strlen');
        if (empty($terms)) {
            return [];
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
        if (empty($orderBy)) {
            $orderBy = ['name' => 'ASC', 'id' => 'ASC'];
        }

        foreach ($orderBy as $sort => $order) {
            $qb->addOrderBy('d.' . $sort, $order);
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
     * Custom findBy so we can filter by related entities
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     *
     * @return array
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT m')->from('IliosCoreBundle:MeshDescriptor', 'm');
        if (empty($orderBy)) {
            $orderBy = ['id' => 'ASC'];
        }
        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('m.'.$sort, $order);
            }
        }

        if (array_key_exists('sessions', $criteria)) {
            $ids = is_array($criteria['sessions']) ?
                $criteria['sessions'] : [$criteria['sessions']];
            $qb->leftJoin('m.sessions', 'session');
            $qb->leftJoin('m.objectives', 'objective');
            $qb->leftJoin('m.sessionLearningMaterials', 'slm');
            $qb->leftJoin('slm.session', 'session2');
            $qb->leftJoin('objective.sessions', 'session3');

            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('session.id', ':sessions'),
                    $qb->expr()->in('session2.id', ':sessions'),
                    $qb->expr()->in('session3.id', ':sessions')
                )
            );
            $qb->setParameter(':sessions', $ids);
        }

        if (array_key_exists('courses', $criteria)) {
            $ids = is_array($criteria['courses']) ?
                $criteria['courses'] : [$criteria['courses']];
            $qb->leftJoin('m.courses', 'course');
            $qb->leftJoin('m.objectives', 'objective');
            $qb->leftJoin('m.sessions', 'session');
            $qb->leftJoin('m.courseLearningMaterials', 'clm');
            $qb->leftJoin('objective.courses', 'course2');
            $qb->leftJoin('clm.course', 'course3');
            $qb->leftJoin('session.course', 'course4');
            $qb->leftJoin('objective.sessions', 'session2');
            $qb->leftJoin('session2.course', 'course5');
            $qb->leftJoin('m.sessionLearningMaterials', 'slm');
            $qb->leftJoin('slm.session', 'session3');
            $qb->leftJoin('session3.course', 'course6');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('course.id', ':courses'),
                    $qb->expr()->in('course2.id', ':courses'),
                    $qb->expr()->in('course3.id', ':courses'),
                    $qb->expr()->in('course4.id', ':courses'),
                    $qb->expr()->in('course5.id', ':courses'),
                    $qb->expr()->in('course6.id', ':courses')
                )
            );
            $qb->setParameter(':courses', $ids);
        }

        if (array_key_exists('sessionTypes', $criteria)) {
            $ids = is_array($criteria['sessionTypes']) ?
                $criteria['sessionTypes'] : [$criteria['sessionTypes']];

            $qb->leftJoin('m.sessions', 'session');
            $qb->leftJoin('m.objectives', 'objective');
            $qb->leftJoin('m.sessionLearningMaterials', 'slm');
            $qb->leftJoin('session.sessionType', 'sessionType');
            $qb->leftJoin('slm.session', 'session2');
            $qb->leftJoin('session2.sessionType', 'sessionType2');
            $qb->leftJoin('objective.sessions', 'session3');
            $qb->leftJoin('session3.sessionType', 'sessionType3');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('sessionType.id', ':sessionTypes'),
                    $qb->expr()->in('sessionType2.id', ':sessionTypes'),
                    $qb->expr()->in('sessionType3.id', ':sessionTypes')
                )
            );
            $qb->setParameter(':sessionTypes', $ids);
        }

        if (array_key_exists('learningMaterials', $criteria)) {
            $ids = is_array($criteria['learningMaterials']) ?
                $criteria['learningMaterials'] : [$criteria['learningMaterials']];
            $qb->leftJoin('m.courseLearningMaterials', 'clm');
            $qb->leftJoin('m.sessionLearningMaterials', 'slm');
            $qb->leftJoin('slm.learningMaterial', 'learningMaterial');
            $qb->leftJoin('clm.learningMaterial', 'learningMaterial2');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('learningMaterial.id', ':lm'),
                    $qb->expr()->in('learningMaterial2.id', ':lm')
                )
            );
            $qb->setParameter(':lm', $ids);
        }

        if (array_key_exists('topics', $criteria)) {
            $ids = is_array($criteria['topics']) ?
                $criteria['topics'] : [$criteria['topics']];
            $qb->leftJoin('m.courses', 'course');
            $qb->leftJoin('m.objectives', 'objective');
            $qb->leftJoin('m.sessions', 'session');
            $qb->leftJoin('m.courseLearningMaterials', 'clm');
            $qb->leftJoin('course.topics', 'topics');
            $qb->leftJoin('objective.courses', 'course2');
            $qb->leftJoin('course2.topics', 'topics2');
            $qb->leftJoin('clm.course', 'course3');
            $qb->leftJoin('course3.topics', 'topics3');
            $qb->leftJoin('session.course', 'course4');
            $qb->leftJoin('session.topics', 'topics4');
            $qb->leftJoin('course4.topics', 'topics5');
            $qb->leftJoin('objective.sessions', 'session2');
            $qb->leftJoin('session2.course', 'course5');
            $qb->leftJoin('session2.topics', 'topics6');
            $qb->leftJoin('course5.topics', 'topics7');
            $qb->leftJoin('m.sessionLearningMaterials', 'slm');
            $qb->leftJoin('slm.session', 'session3');
            $qb->leftJoin('session3.course', 'course6');
            $qb->leftJoin('session3.topics', 'topics8');
            $qb->leftJoin('course6.topics', 'topics9');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('topics.id', ':topics'),
                    $qb->expr()->in('topics2.id', ':topics'),
                    $qb->expr()->in('topics3.id', ':topics'),
                    $qb->expr()->in('topics4.id', ':topics'),
                    $qb->expr()->in('topics5.id', ':topics'),
                    $qb->expr()->in('topics6.id', ':topics'),
                    $qb->expr()->in('topics7.id', ':topics'),
                    $qb->expr()->in('topics8.id', ':topics'),
                    $qb->expr()->in('topics9.id', ':topics')
                )
            );
            $qb->setParameter(':topics', $ids);
        }

        unset($criteria['courses']);
        unset($criteria['sessions']);
        unset($criteria['sessionTypes']);
        unset($criteria['learningMaterials']);
        unset($criteria['topics']);

        if (count($criteria)) {
            foreach ($criteria as $key => $value) {
                $values = is_array($value) ? $value : [$value];
                $qb->andWhere($qb->expr()->in("m.{$key}", ":{$key}"));
                $qb->setParameter(":{$key}", $values);
            }
        }
        if ($offset) {
            $qb->setFirstResult($offset);
        }
        if ($limit) {
            $qb->setMaxResults($limit);
        }
        return $qb->getQuery()->getResult();
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
