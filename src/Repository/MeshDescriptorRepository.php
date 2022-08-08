<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\MeshConcept;
use App\Entity\MeshDescriptor;
use App\Entity\MeshPreviousIndexing;
use App\Entity\MeshQualifier;
use App\Entity\MeshTerm;
use App\Entity\MeshTree;
use App\Service\DTOCacheManager;
use App\Service\MeshDescriptorSetTransmogrifier;
use App\Traits\ManagerRepository;
use DateTime;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use App\Entity\DTO\MeshDescriptorDTO;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Ilios\MeSH\Model\AllowableQualifier;
use Ilios\MeSH\Model\Concept;
use Ilios\MeSH\Model\Descriptor;
use Ilios\MeSH\Model\DescriptorSet;
use Ilios\MeSH\Model\Term;
use PDO;

use function array_values;
use function array_keys;

class MeshDescriptorRepository extends ServiceEntityRepository implements
    DTORepositoryInterface,
    RepositoryInterface
{
    use ManagerRepository;

    public function __construct(
        ManagerRegistry $registry,
        protected MeshDescriptorSetTransmogrifier $transmogrifier,
        protected DTOCacheManager $cacheManager,
    ) {
        parent::__construct($registry, MeshDescriptor::class);
    }

    /**
     * Find by a string query.
     */
    public function findDTOsByQ(
        string $q,
        ?array $orderBy,
        ?int $limit,
        ?int $offset
    ): array {
        $terms = $this->getTermsFromQ($q);
        if (empty($terms)) {
            return [];
        }

        $query = $this->getQueryForFindByQ($terms, $orderBy, $offset);
        $dtos = $this->createDTOs($query);

        // Unfortunately, we can't let Doctrine limit the fetch here because of all the joins
        // it returns many less than the desired number.
        if ($limit) {
            $dtos = array_slice($dtos, 0, $limit);
        }

        return $dtos;
    }

    public function hydrateDTOsFromIds(array $ids): array
    {
        $qb = $this->_em->createQueryBuilder()->select('x')->distinct()->from(MeshDescriptor::class, 'x');
        $qb->where($qb->expr()->in('x.id', ':ids'));
        $qb->setParameter(':ids', $ids);

        return $this->createDTOs($qb->getQuery());
    }

    /**
     * Hydrate as DTOs
     */
    protected function createDTOs(AbstractQuery $query): array
    {
        $dtos = [];
        foreach ($query->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $dtos[$arr['id']] = new MeshDescriptorDTO(
                $arr['id'],
                $arr['name'],
                $arr['annotation'],
                $arr['createdAt'],
                $arr['updatedAt'],
                $arr['deleted']
            );
        }
        $descriptorIds = array_keys($dtos);
        $qb = $this->_em->createQueryBuilder();
        $qb->select('p.id AS prevId, m.id AS descriptorId')
            ->from('App\Entity\MeshPreviousIndexing', 'p')
            ->join('p.descriptor', 'm')
            ->where($qb->expr()->in('m.id', ':descriptorIds'))
            ->setParameter('descriptorIds', $descriptorIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $dtos[$arr['descriptorId']]->previousIndexing = (int) $arr['prevId'];
        }

        $dtos = $this->attachRelatedToDtos(
            $dtos,
            [
                'courses',
                'sessions',
                'concepts',
                'qualifiers',
                'trees',
                'sessionLearningMaterials',
                'courseLearningMaterials',
                'sessionObjectives',
                'courseObjectives',
                'programYearObjectives',
            ],
        );

        return array_values($dtos);
    }

    protected function getTermsFromQ(string $q)
    {
        $terms = explode(' ', $q);
        return array_filter($terms, 'strlen');
    }
    protected function getQueryForFindByQ(array $terms, ?array $orderBy, ?int $offset): Query
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('DISTINCT d')
            ->from(MeshDescriptor::class, 'd')
            ->leftJoin('d.previousIndexing', 'pi')
            ->leftJoin('d.concepts', 'c')
            ->leftJoin('c.terms', 't')
            ->andWhere('d.deleted = false');

        foreach ($terms as $key => $term) {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('d.id', "?{$key}"),
                $qb->expr()->like('d.name', "?{$key}"),
                $qb->expr()->like('d.annotation', "?{$key}"),
                $qb->expr()->like('pi.previousIndexing', "?{$key}"),
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
        $query->enableResultCache(3600);
        return $query;
    }

    protected function attachCriteriaToQueryBuilder(
        QueryBuilder $qb,
        array $criteria,
        ?array $orderBy,
        ?int $limit,
        ?int $offset
    ): void {
        if (array_key_exists('sessions', $criteria)) {
            $ids = is_array($criteria['sessions']) ?
                $criteria['sessions'] : [$criteria['sessions']];
            $qb->leftJoin('x.sessions', 'session');
            $qb->leftJoin('x.sessionObjectives', 'sessionObjective');
            $qb->leftJoin('x.sessionLearningMaterials', 'slm');
            $qb->leftJoin('slm.session', 'session2');
            $qb->leftJoin('sessionObjective.session', 'session3');

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
            $qb->leftJoin('x.courses', 'course');
            $qb->leftJoin('x.sessions', 'session');
            $qb->leftJoin('x.courseLearningMaterials', 'clm');
            $qb->leftJoin('x.courseObjectives', 'courseObjective');
            $qb->leftJoin('courseObjective.course', 'course2');
            $qb->leftJoin('clm.course', 'course3');
            $qb->leftJoin('session.course', 'course4');
            $qb->leftJoin('x.sessionObjectives', 'sessionObjective');
            $qb->leftJoin('sessionObjective.session', 'session2');
            $qb->leftJoin('session2.course', 'course5');
            $qb->leftJoin('x.sessionLearningMaterials', 'slm');
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

            $qb->leftJoin('x.sessions', 'session');
            $qb->leftJoin('x.sessionLearningMaterials', 'slm');
            $qb->leftJoin('session.sessionType', 'sessionType');
            $qb->leftJoin('slm.session', 'session2');
            $qb->leftJoin('session2.sessionType', 'sessionType2');
            $qb->leftJoin('x.sessionObjectives', 'sessionObjective');
            $qb->leftJoin('sessionObjective.session', 'session3');

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
            $qb->leftJoin('x.courseLearningMaterials', 'clm');
            $qb->leftJoin('x.sessionLearningMaterials', 'slm');
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

        if (array_key_exists('terms', $criteria)) {
            $ids = is_array($criteria['terms']) ?
                $criteria['terms'] : [$criteria['terms']];
            $qb->leftJoin('x.courses', 'course');
            $qb->leftJoin('x.sessions', 'session');
            $qb->leftJoin('x.courseLearningMaterials', 'clm');
            $qb->leftJoin('course.terms', 'terms');
            $qb->leftJoin('x.courseObjectives', 'courseObjectives');
            $qb->leftJoin('courseObjectives.course', 'course2');
            $qb->leftJoin('course2.terms', 'terms2');
            $qb->leftJoin('clm.course', 'course3');
            $qb->leftJoin('course3.terms', 'terms3');
            $qb->leftJoin('session.course', 'course4');
            $qb->leftJoin('session.terms', 'terms4');
            $qb->leftJoin('course4.terms', 'terms5');
            $qb->leftJoin('x.sessionObjectives', 'sessionObjective');
            $qb->leftJoin('sessionObjective.session', 'session2');
            $qb->leftJoin('session2.course', 'course5');
            $qb->leftJoin('session2.terms', 'terms6');
            $qb->leftJoin('course5.terms', 'terms7');
            $qb->leftJoin('x.sessionLearningMaterials', 'slm');
            $qb->leftJoin('slm.session', 'session3');
            $qb->leftJoin('session3.course', 'course6');
            $qb->leftJoin('session3.terms', 'terms8');
            $qb->leftJoin('course6.terms', 'terms9');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('terms.id', ':terms'),
                    $qb->expr()->in('terms2.id', ':terms'),
                    $qb->expr()->in('terms3.id', ':terms'),
                    $qb->expr()->in('terms4.id', ':terms'),
                    $qb->expr()->in('terms5.id', ':terms'),
                    $qb->expr()->in('terms6.id', ':terms'),
                    $qb->expr()->in('terms7.id', ':terms'),
                    $qb->expr()->in('terms8.id', ':terms'),
                    $qb->expr()->in('terms9.id', ':terms')
                )
            );
            $qb->setParameter(':terms', $ids);
        }

        unset($criteria['courses']);
        unset($criteria['sessions']);
        unset($criteria['sessionTypes']);
        unset($criteria['learningMaterials']);
        unset($criteria['terms']);

        $this->attachClosingCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
    }

    /**
     * Gut the MeSH tables, leaving only descriptor records in place that are somehow wired up to the rest of Ilios.
     * @throws Exception
     */
    public function clearExistingData()
    {
        $conn = $this->_em->getConnection();
        $conn->beginTransaction();
        try {
            $conn->executeQuery('DELETE FROM mesh_concept_x_term');
            $conn->executeQuery('DELETE FROM mesh_descriptor_x_qualifier');
            $conn->executeQuery('DELETE FROM mesh_descriptor_x_concept');
            $conn->executeQuery('DELETE FROM mesh_previous_indexing');
            $conn->executeQuery('DELETE FROM mesh_tree');
            $conn->executeQuery('DELETE FROM mesh_term');
            $conn->executeQuery('DELETE FROM mesh_concept');
            $conn->executeQuery('DELETE FROM mesh_qualifier');

            $sql = <<<EOL
DELETE FROM mesh_descriptor
WHERE mesh_descriptor_uid NOT IN (SELECT mesh_descriptor_uid FROM course_learning_material_x_mesh)
AND mesh_descriptor_uid NOT IN (SELECT mesh_descriptor_uid FROM session_learning_material_x_mesh)
AND mesh_descriptor_uid NOT IN (SELECT mesh_descriptor_uid FROM course_x_mesh)
AND mesh_descriptor_uid NOT IN (SELECT mesh_descriptor_uid FROM session_x_mesh)
AND mesh_descriptor_uid NOT IN (SELECT mesh_descriptor_uid FROM session_objective_x_mesh)
AND mesh_descriptor_uid NOT IN (SELECT mesh_descriptor_uid FROM course_objective_x_mesh)
AND mesh_descriptor_uid NOT IN (SELECT mesh_descriptor_uid FROM program_year_objective_x_mesh)
AND mesh_descriptor_uid NOT IN (
  SELECT prepositional_object_table_row_id FROM report where prepositional_object = 'mesh term'
)
EOL;
            $conn->executeQuery($sql);
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    public function upsertMeshUniverse(DescriptorSet $descriptorSet, array $existingDescriptorIds)
    {
        $data = $this->transmogrifier->transmogrify($descriptorSet, $existingDescriptorIds);
        $now = new DateTime();
        $conn = $this->_em->getConnection();

        $termMap = []; // maps term hashes to record ids.
        $conn->beginTransaction();
        try {
            /* @var Descriptor $descriptor */
            foreach ($data['insert']['mesh_descriptor'] as $descriptor) {
                    $conn->insert('mesh_descriptor', [
                        'mesh_descriptor_uid' => $descriptor->getUi(),
                        'name' => $descriptor->getName(),
                        'annotation' => $descriptor->getAnnotation(),
                        'deleted' => false,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ], [
                        PDO::PARAM_STR,
                        PDO::PARAM_STR,
                        PDO::PARAM_STR,
                        PDO::PARAM_BOOL,
                        'datetime',
                        'datetime',
                    ]);
            }
            /* @var Descriptor $descriptor */
            foreach ($data['update']['mesh_descriptor'] as $descriptor) {
                $conn->update('mesh_descriptor', [
                    'name' => $descriptor->getName(),
                    'annotation' => $descriptor->getAnnotation(),
                    'updated_at' => $now,
                ], [
                    'mesh_descriptor_uid' => $descriptor->getUi()
                ], [
                    PDO::PARAM_STR,
                    PDO::PARAM_STR,
                    'datetime',
                ]);
            }
            /* @var AllowableQualifier $qualifier */
            foreach ($data['insert']['mesh_qualifier'] as $qualifier) {
                $conn->insert('mesh_qualifier', [
                    'mesh_qualifier_uid' => $qualifier->getQualifierReference()->getUi(),
                    'name' => $qualifier->getQualifierReference()->getName(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ], [
                    PDO::PARAM_STR,
                    PDO::PARAM_STR,
                    'datetime',
                    'datetime'
                ]);
            }
            /* @var Concept $concept */
            foreach ($data['insert']['mesh_concept'] as $concept) {
                $conn->insert('mesh_concept', [
                    'mesh_concept_uid' => $concept->getUi(),
                    'name' => $concept->getName(),
                    'preferred' => $concept->isPreferred(),
                    'scope_note' => $concept->getScopeNote(),
                    'casn_1_name' => $concept->getCasn1Name(),
                    'registry_number' => $concept->getRegistryNumber(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ], [
                    PDO::PARAM_STR,
                    PDO::PARAM_STR,
                    PDO::PARAM_BOOL,
                    PDO::PARAM_STR,
                    PDO::PARAM_STR,
                    PDO::PARAM_STR,
                    'datetime',
                    'datetime'
                ]);
            }
            /* @var Term $term */
            $i = 1;
            foreach ($data['insert']['mesh_term'] as $hash => $term) {
                $conn->insert('mesh_term', [
                    'mesh_term_id' => $i,
                    'mesh_term_uid' => $term->getUi(),
                    'name' => $term->getName(),
                    'lexical_tag' => $term->getLexicalTag(),
                    'concept_preferred' => $term->isConceptPreferred(),
                    'record_preferred' => $term->isRecordPreferred(),
                    'permuted' => $term->isPermuted(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ], [
                    PDO::PARAM_INT,
                    PDO::PARAM_STR,
                    PDO::PARAM_STR,
                    PDO::PARAM_STR,
                    PDO::PARAM_BOOL,
                    PDO::PARAM_BOOL,
                    PDO::PARAM_BOOL,
                    'datetime',
                    'datetime',
                ]);
                $termMap[$hash] = $i;
                $i++;
            }

            foreach ($data['insert']['mesh_descriptor_x_concept'] as $ref) {
                $conn->insert('mesh_descriptor_x_concept', [
                    'mesh_descriptor_uid' => $ref[0],
                    'mesh_concept_uid' => $ref[1],
                ]);
            }
            foreach ($data['insert']['mesh_descriptor_x_qualifier'] as $ref) {
                $conn->insert('mesh_descriptor_x_qualifier', [
                    'mesh_descriptor_uid' => $ref[0],
                    'mesh_qualifier_uid' => $ref[1],
                ]);
            }
            foreach ($data['insert']['mesh_concept_x_term'] as $ref) {
                $conn->insert('mesh_concept_x_term', [
                    'mesh_concept_uid' => $ref[0],
                    'mesh_term_id' => $termMap[$ref[1]],
                ]);
            }
            foreach ($data['insert']['mesh_previous_indexing'] as $descriptorUi => $previousIndexing) {
                $conn->insert('mesh_previous_indexing', [
                    'mesh_descriptor_uid' => $descriptorUi,
                    'previous_indexing' => $previousIndexing,
                ]);
            }
            foreach ($data['insert']['mesh_tree'] as $descriptorUi => $trees) {
                foreach ($trees as $tree) {
                    $conn->insert('mesh_tree', [
                        'mesh_descriptor_uid' => $descriptorUi,
                        'tree_number' => $tree,
                    ]);
                }
            }

            $conn->commit();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    /**
     * Flag all given MeSH descriptors as "deleted".
     * @param array $ids The mesh descriptor IDs.
     */
    public function flagDescriptorsAsDeleted(array $ids)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->update(MeshDescriptor::class, 'm');
        $qb->set('m.deleted', ':deleted');
        $qb->where($qb->expr()->in('m.id', ':ids'));
        $qb->setParameter(':deleted', true);
        $qb->setParameter(':ids', $ids);
        $query = $qb->getQuery();
        $query->execute();
    }

    /**
     * Get all the IDs
     */
    public function getIds(): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->addSelect('x.id')->from(MeshDescriptor::class, 'x');

        return array_map(fn(array $arr) => $arr['id'], $qb->getQuery()->getScalarResult());
    }

    /**
     * Create Descriptor objects for a set of Ids
     *
     * @todo these are not complete, they only have the information needed for the search Index
     */
    public function getIliosMeshDescriptorsById(array $ids): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('d.id, d.name, d.annotation, pi.previousIndexing')
            ->from(MeshDescriptor::class, 'd')
            ->leftJoin('d.previousIndexing', 'pi')
            ->where('d.deleted = false')
            ->andWhere($qb->expr()->in('d.id', ':ids'))
            ->setParameter('ids', $ids);
        $descriptors = $qb->getQuery()->getArrayResult();
        $unDeletedIds = array_map(fn(array $arr) => $arr['id'], $descriptors);

        $qb = $this->_em->createQueryBuilder();
        $qb->select('d.id as descriptorId, c.id, c.name, c.scopeNote, c.casn1Name')
            ->from(MeshConcept::class, 'c')
            ->join('c.descriptors', 'd')
            ->andWhere($qb->expr()->in('d.id', ':ids'))
            ->setParameter('ids', $unDeletedIds);
        $concepts = $qb->getQuery()->getArrayResult();
        $conceptIds = array_map(fn(array $arr) => $arr['id'], $concepts);

        $qb = $this->_em->createQueryBuilder();
        $qb->select('c.id as conceptId, t.id, t.name')
            ->from(MeshTerm::class, 't')
            ->join('t.concepts', 'c')
            ->andWhere($qb->expr()->in('c.id', ':ids'))
            ->setParameter('ids', $conceptIds);
        $terms = $qb->getQuery()->getArrayResult();

        $fullConcepts = array_map(function (array $concept) use ($terms) {
            $conceptId = $concept['id'];
            $concept['terms'] = array_filter($terms, fn(array $term) => $term['conceptId'] == $conceptId);

            return $concept;
        }, $concepts);

        $fullDescriptors = array_map(function (array $descriptor) use ($fullConcepts) {
            $descriptorId = $descriptor['id'];
            $descriptor['concepts'] = array_filter(
                $fullConcepts,
                fn(array $concept) => $concept['descriptorId'] == $descriptorId
            );

            return $descriptor;
        }, $descriptors);

        return array_map(function (array $arr) {
            $descriptor = new Descriptor();
            $descriptor->setUi($arr['id']);
            $descriptor->setName($arr['name']);
            $descriptor->setAnnotation($arr['annotation']);
            $descriptor->addPreviousIndexing($arr['previousIndexing']);
            foreach ($arr['concepts'] as $arr) {
                $concept = new Concept();
                $concept->setUi($arr['id']);
                $concept->setName($arr['name']);
                $concept->setCasn1Name($arr['casn1Name']);
                $concept->setScopeNote($arr['scopeNote']);
                foreach ($arr['terms'] as $a) {
                    $term = new Term();
                    $term->setName($a['name']);
                    $concept->addTerm($term);
                }
                $descriptor->addConcept($concept);
            }

            return $descriptor;
        }, $fullDescriptors);
    }

    public function exportMeshDescriptors(): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('d.id, d.name, d.annotation, d.deleted')
            ->from(MeshDescriptor::class, 'd')
            ->orderBy('d.id');
        return $qb->getQuery()->getScalarResult();
    }

    public function exportMeshTrees(): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('t.treeNumber, d.id AS descriptor_id, t.id')
            ->from(MeshTree::class, 't')
            ->join('t.descriptor', 'd')
            ->orderBy('t.id');
        return $qb->getQuery()->getScalarResult();
    }

    public function exportMeshConcepts(): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('c.id, c.name, c.preferred, c.scopeNote, c.casn1Name, c.registryNumber')
            ->from(MeshConcept::class, 'c')
            ->orderBy('c.id');
        return $qb->getQuery()->getScalarResult();
    }

    public function exportMeshTerms(): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('t.meshTermUid, t.name, t.lexicalTag, t.conceptPreferred, t.recordPreferred, t.permuted, t.id')
            ->from(MeshTerm::class, 't')
            ->orderBy('t.id');
        return $qb->getQuery()->getScalarResult();
    }

    public function exportMeshQualifiers(): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('q.id, q.name')
            ->from(MeshQualifier::class, 'q')
            ->orderBy('q.id')
            ->addOrderBy('q.name');
        return $qb->getQuery()->getScalarResult();
    }

    public function exportMeshPreviousIndexings(): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('d.id AS descriptor_id, p.previousIndexing, p.id')
            ->from(MeshPreviousIndexing::class, 'p')
            ->join('p.descriptor', 'd')
            ->orderBy('p.id');
        return $qb->getQuery()->getScalarResult();
    }

    public function exportMeshConceptTerms(): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('c.id AS concept_id, t.id AS term_id')
            ->from(MeshConcept::class, 'c')
            ->join('c.terms', 't')
            ->orderBy('t.id')
            ->addOrderBy('c.id');
        return $qb->getQuery()->getScalarResult();
    }

    public function exportMeshDescriptorQualifiers(): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('d.id AS descriptor_id, q.id AS qualifier_id')
            ->from(MeshDescriptor::class, 'd')
            ->join('d.qualifiers', 'q')
            ->orderBy('d.id')
            ->addOrderBy('q.id');
        return $qb->getQuery()->getScalarResult();
    }

    public function exportMeshDescriptorConcepts(): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('c.id AS concept_id, d.id AS descriptor_id')
            ->from(MeshDescriptor::class, 'd')
            ->join('d.concepts', 'c')
            ->orderBy('c.id')
            ->addOrderBy('d.id');
        return $qb->getQuery()->getScalarResult();
    }
}
