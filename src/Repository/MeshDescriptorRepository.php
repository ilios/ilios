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
use Doctrine\DBAL\ParameterType;
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
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('x')
            ->distinct()
            ->from(MeshDescriptor::class, 'x');
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
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('p.id AS prevId, m.id AS descriptorId')
            ->from(MeshPreviousIndexing::class, 'p')
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

    protected function getTermsFromQ(string $q): array
    {
        $terms = explode(' ', $q);
        return array_filter($terms, 'strlen');
    }
    protected function getQueryForFindByQ(array $terms, ?array $orderBy, ?int $offset): Query
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
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
        $joins = [];

        $criteriaKeys = array_keys($criteria);

        if (
            array_intersect(
                $criteriaKeys,
                ['sessions', 'courses', 'sessionTypes', 'terms', 'schools']
            )
        ) {
            $joins['x.sessions'] = 'sessions';
            $joins['x.sessionObjectives'] = 'sessionObjectives';
            $joins['sessionObjectives.session'] = 'sessionObjectives_session';
            $joins['x.sessionLearningMaterials'] = 'sessionLearningMaterials';
            $joins['sessionLearningMaterials.session'] = 'sessionLearningMaterials_session';
        }
        if (
            array_intersect(
                $criteriaKeys,
                ['courses', 'terms', 'schools']
            )
        ) {
            $joins['x.courses'] = 'courses';
            $joins['x.courseObjectives'] = 'courseObjectives';
            $joins['courseObjectives.course'] = 'courseObjectives_course';
            $joins['x.courseLearningMaterials'] = 'courseLearningMaterials';
            $joins['courseLearningMaterials.course'] = 'courseLearningMaterials_course';
        }
        if (
            array_intersect(
                $criteriaKeys,
                ['terms', 'schools']
            )
        ) {
            $joins['sessions.course'] = 'sessions_course';
            $joins['sessionObjectives_session.course'] = 'sessionObjectives_session_course';
            $joins['sessionLearningMaterials_session.course'] = 'sessionLearningMaterials_session_course';
        }

        if (array_key_exists('courses', $criteria)) {
            $joins['sessions.course'] = 'sessions_course';
            $joins['sessionObjectives_session.course'] = 'sessionObjectives_session_course';
            $joins['sessionLearningMaterials_session.course'] = 'sessionLearningMaterials_session_course';
        }
        if (array_key_exists('sessionTypes', $criteria)) {
            $joins['sessions.sessionType'] = 'sessions_sessionType';
            $joins['sessionObjectives_session.sessionType'] = 'sessionObjectives_session_sessionType';
            $joins['sessionLearningMaterials_session.sessionType'] = 'sessionLearningMaterials_session_sessionType';
        }
        if (array_key_exists('learningMaterials', $criteria)) {
            $joins['x.sessions'] = 'sessions';
            $joins['x.sessionLearningMaterials'] = 'sessionLearningMaterials';
            $joins['sessionLearningMaterials.learningMaterial'] = 'sessionLearningMaterials_learningMaterial';

            $joins['x.courses'] = 'courses';
            $joins['x.courseLearningMaterials'] = 'courseLearningMaterials';
            $joins['courseLearningMaterials.learningMaterial'] = 'courseLearningMaterials_learningMaterial';
        }

        if (array_key_exists('terms', $criteria)) {
            $joins['sessions.terms'] = 'sessions_terms';
            $joins['sessionObjectives_session.terms'] = 'sessionObjectives_session_terms';
            $joins['sessionLearningMaterials_session.terms'] = 'sessionLearningMaterials_session_terms';

            $joins['courses.terms'] = 'courses_terms';
            $joins['courseObjectives_course.terms'] = 'courseObjectives_course_terms';
            $joins['courseLearningMaterials_course.terms'] = 'courseLearningMaterials_course_terms';

            $joins['sessions_course.terms'] = 'sessions_course_terms';
            $joins['sessionObjectives_session_course.terms'] = 'sessionObjectives_session_course_terms';
            $joins['sessionLearningMaterials_session_course.terms'] = 'sessionLearningMaterials_session_course_terms';
        }

        if (array_key_exists('schools', $criteria)) {
            $joins['sessions.course'] = 'sessions_course';
            $joins['sessionObjectives_session.course'] = 'sessionObjectives_session_course';
            $joins['sessionLearningMaterials_session.course'] = 'sessionLearningMaterials_session_course';

            $joins['courses.school'] = 'courses_school';
            $joins['courseObjectives_course.school'] = 'courseObjectives_course_school';
            $joins['courseLearningMaterials_course.school'] = 'courseLearningMaterials_course_school';
            $joins['sessions_course.school'] = 'sessions_course_school';
            $joins['sessionObjectives_session_course.school'] = 'sessionObjectives_session_course_school';
            $joins['sessionLearningMaterials_session_course.school'] = 'sessionLearningMaterials_session_course_school';

            $joins['courses.cohorts'] = 'courses_cohorts';
            $joins['courseObjectives_course.cohorts'] = 'courseObjectives_cc';
            $joins['courseLearningMaterials_course.cohorts'] = 'courseLearningMaterials_cc';
            $joins['sessions_course.cohorts'] = 'sessions_cc';
            $joins['sessionObjectives_session_course.cohorts'] = 'sessionObjectives_session_cc';
            $joins['sessionLearningMaterials_session_course.cohorts'] = 'sessionLearningMaterials_session_cc';

            $joins['courses_cohorts.programYear'] = 'cc_py';
            $joins['courseObjectives_cc.programYear'] = 'courseObjectives_cc_py';
            $joins['courseLearningMaterials_cc.programYear'] = 'courseLearningMaterials_cc_py';
            $joins['sessions_cc.programYear'] = 'sessions_cc_py';
            $joins['sessionObjectives_session_cc.programYear'] = 'sessionObjectives_session_cc_py';
            $joins['sessionLearningMaterials_session_cc.programYear'] = 'sessionLearningMaterials_session_cc_py';

            $joins['cc_py.program'] = 'cc_pyp';
            $joins['courseObjectives_cc_py.program'] = 'courseObjectives_cc_pyp';
            $joins['courseLearningMaterials_cc_py.program'] = 'courseLearningMaterials_cc_pyp';
            $joins['sessions_cc_py.program'] = 'sessions_cc_pyp';
            $joins['sessionObjectives_session_cc_py.program'] = 'sessionObjectives_session_cc_pyp';
            $joins['sessionLearningMaterials_session_cc_py.program'] = 'sessionLearningMaterials_session_cc_pyp';

            $joins['cc_pyp.school'] = 'cc_pyp_school';
            $joins['courseObjectives_cc_pyp.school'] = 'courseObjectives_cc_pyp_school';
            $joins['courseLearningMaterials_cc_pyp.school'] = 'courseLearningMaterials_cc_pyp_school';
            $joins['sessions_cc_pyp.school'] = 'sessions_cc_pyp_school';
            $joins['sessionObjectives_session_cc_pyp.school'] = 'sessionObjectives_session_cc_pyp_school';
            $joins['sessionLearningMaterials_session_cc_pyp.school'] = 'sessionLearningMaterials_session_cc_pyp_school';
        }

        foreach ($joins as $join => $alias) {
            $qb->leftJoin($join, $alias);
        }

        if (array_key_exists('sessions', $criteria)) {
            $ids = is_array($criteria['sessions']) ? $criteria['sessions'] : [$criteria['sessions']];
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('sessions.id', ':sessions'),
                    $qb->expr()->in('sessionObjectives_session.id', ':sessions'),
                    $qb->expr()->in('sessionLearningMaterials_session.id', ':sessions')
                )
            );
            $qb->setParameter(':sessions', $ids);
            unset($criteria['sessions']);
        }

        if (array_key_exists('courses', $criteria)) {
            $ids = is_array($criteria['courses']) ? $criteria['courses'] : [$criteria['courses']];
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('courses.id', ':courses'),
                    $qb->expr()->in('courseObjectives_course.id', ':courses'),
                    $qb->expr()->in('courseLearningMaterials_course.id', ':courses'),
                    $qb->expr()->in('sessions_course.id', ':courses'),
                    $qb->expr()->in('sessionObjectives_session_course.id', ':courses'),
                    $qb->expr()->in('sessionLearningMaterials_session_course.id', ':courses'),
                )
            );
            $qb->setParameter(':courses', $ids);
            unset($criteria['courses']);
        }
        if (array_key_exists('sessionTypes', $criteria)) {
            $ids = is_array($criteria['sessionTypes']) ? $criteria['sessionTypes'] : [$criteria['sessionTypes']];
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('sessions_sessionType.id', ':sessionTypes'),
                    $qb->expr()->in('sessionObjectives_session_sessionType.id', ':sessionTypes'),
                    $qb->expr()->in('sessionLearningMaterials_session_sessionType.id', ':sessionTypes')
                )
            );
            $qb->setParameter(':sessionTypes', $ids);
            unset($criteria['sessionTypes']);
        }
        if (array_key_exists('learningMaterials', $criteria)) {
            $ids = is_array(
                $criteria['learningMaterials']
            ) ? $criteria['learningMaterials'] : [$criteria['learningMaterials']];
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('sessionLearningMaterials_learningMaterial.id', ':learningMaterials'),
                    $qb->expr()->in('courseLearningMaterials_learningMaterial.id', ':learningMaterials'),
                )
            );
            $qb->setParameter(':learningMaterials', $ids);
            unset($criteria['learningMaterials']);
        }
        if (array_key_exists('terms', $criteria)) {
            $ids = is_array($criteria['terms']) ? $criteria['terms'] : [$criteria['terms']];
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('sessions_terms.id', ':terms'),
                    $qb->expr()->in('sessionObjectives_session_terms.id', ':terms'),
                    $qb->expr()->in('sessionLearningMaterials_session_terms.id', ':terms'),
                    $qb->expr()->in('courses_terms.id', ':terms'),
                    $qb->expr()->in('courseObjectives_course_terms.id', ':terms'),
                    $qb->expr()->in('courseLearningMaterials_course_terms.id', ':terms'),
                    $qb->expr()->in('sessions_course_terms.id', ':terms'),
                    $qb->expr()->in('sessionObjectives_session_course_terms.id', ':terms'),
                    $qb->expr()->in('sessionLearningMaterials_session_course_terms.id', ':terms'),
                )
            );
            $qb->setParameter(':terms', $ids);
            unset($criteria['terms']);
        }

        if (array_key_exists('schools', $criteria)) {
            $ids = is_array($criteria['schools']) ? $criteria['schools'] : [$criteria['schools']];
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('courses_school.id', ':schools'),
                    $qb->expr()->in('courseObjectives_course_school.id', ':schools'),
                    $qb->expr()->in('courseLearningMaterials_course_school.id', ':schools'),
                    $qb->expr()->in('sessions_course_school.id', ':schools'),
                    $qb->expr()->in('sessionObjectives_session_course_school.id', ':schools'),
                    $qb->expr()->in('sessionLearningMaterials_session_course_school.id', ':schools'),
                    $qb->expr()->in('cc_pyp_school.id', ':schools'),
                    $qb->expr()->in('courseObjectives_cc_pyp_school.id', ':schools'),
                    $qb->expr()->in('courseLearningMaterials_cc_pyp_school.id', ':schools'),
                    $qb->expr()->in('sessions_cc_pyp_school.id', ':schools'),
                    $qb->expr()->in('sessionObjectives_session_cc_pyp_school.id', ':schools'),
                    $qb->expr()->in('sessionLearningMaterials_session_cc_pyp_school.id', ':schools'),
                )
            );
            $qb->setParameter(':schools', $ids);
            unset($criteria['schools']);
        }

        $this->attachClosingCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
    }

    /**
     * Gut the MeSH tables, leaving only descriptor records in place that are somehow wired up to the rest of Ilios.
     * @throws Exception
     */
    public function clearExistingData(): void
    {
        $conn = $this->getEntityManager()->getConnection();
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
    public function upsertMeshUniverse(DescriptorSet $descriptorSet, array $existingDescriptorIds): void
    {
        $data = $this->transmogrifier->transmogrify($descriptorSet, $existingDescriptorIds);
        $now = new DateTime();
        $conn = $this->getEntityManager()->getConnection();

        $termMap = []; // maps term hashes to record ids.
        $conn->beginTransaction();
        try {
            /** @var Descriptor $descriptor */
            foreach ($data['insert']['mesh_descriptor'] as $descriptor) {
                    $conn->insert('mesh_descriptor', [
                        'mesh_descriptor_uid' => $descriptor->getUi(),
                        'name' => $descriptor->getName(),
                        'annotation' => $descriptor->getAnnotation(),
                        'deleted' => false,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ], [
                        ParameterType::STRING,
                        ParameterType::STRING,
                        ParameterType::STRING,
                        ParameterType::BOOLEAN,
                        'datetime',
                        'datetime',
                    ]);
            }
            /** @var Descriptor $descriptor */
            foreach ($data['update']['mesh_descriptor'] as $descriptor) {
                $conn->update('mesh_descriptor', [
                    'name' => $descriptor->getName(),
                    'annotation' => $descriptor->getAnnotation(),
                    'updated_at' => $now,
                ], [
                    'mesh_descriptor_uid' => $descriptor->getUi(),
                ], [
                    ParameterType::STRING,
                    ParameterType::STRING,
                    'datetime',
                ]);
            }
            /** @var AllowableQualifier $qualifier */
            foreach ($data['insert']['mesh_qualifier'] as $qualifier) {
                $conn->insert('mesh_qualifier', [
                    'mesh_qualifier_uid' => $qualifier->getQualifierReference()->getUi(),
                    'name' => $qualifier->getQualifierReference()->getName(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ], [
                    ParameterType::STRING,
                    ParameterType::STRING,
                    'datetime',
                    'datetime',
                ]);
            }
            /** @var Concept $concept */
            foreach ($data['insert']['mesh_concept'] as $concept) {
                $conn->insert('mesh_concept', [
                    'mesh_concept_uid' => $concept->getUi(),
                    'name' => $concept->getName(),
                    'preferred' => $concept->isPreferred(),
                    'scope_note' => $concept->getScopeNote(),
                    'casn_1_name' => $concept->getCasn1Name(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ], [
                    ParameterType::STRING,
                    ParameterType::STRING,
                    ParameterType::BOOLEAN,
                    ParameterType::STRING,
                    ParameterType::STRING,
                    'datetime',
                    'datetime',
                ]);
            }
            $i = 1;
            /** @var Term $term */
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
                    ParameterType::INTEGER,
                    ParameterType::STRING,
                    ParameterType::STRING,
                    ParameterType::STRING,
                    ParameterType::BOOLEAN,
                    ParameterType::BOOLEAN,
                    ParameterType::BOOLEAN,
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
    public function flagDescriptorsAsDeleted(array $ids): void
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
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
        $qb = $this->getEntityManager()->createQueryBuilder();
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
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('d.id, d.name, d.annotation, pi.previousIndexing')
            ->from(MeshDescriptor::class, 'd')
            ->leftJoin('d.previousIndexing', 'pi')
            ->where('d.deleted = false')
            ->andWhere($qb->expr()->in('d.id', ':ids'))
            ->setParameter('ids', $ids);
        $descriptors = $qb->getQuery()->getArrayResult();
        $unDeletedIds = array_map(fn(array $arr) => $arr['id'], $descriptors);

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('d.id as descriptorId, c.id, c.name, c.scopeNote, c.casn1Name')
            ->from(MeshConcept::class, 'c')
            ->join('c.descriptors', 'd')
            ->andWhere($qb->expr()->in('d.id', ':ids'))
            ->setParameter('ids', $unDeletedIds);
        $concepts = $qb->getQuery()->getArrayResult();
        $conceptIds = array_map(fn(array $arr) => $arr['id'], $concepts);

        $qb = $this->getEntityManager()->createQueryBuilder();
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
            if (!is_null($arr['previousIndexing'])) {
                $descriptor->addPreviousIndexing($arr['previousIndexing']);
            }
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
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('d.id, d.name, d.annotation, d.deleted')
            ->from(MeshDescriptor::class, 'd')
            ->orderBy('d.id');
        return $qb->getQuery()->getScalarResult();
    }

    public function exportMeshTrees(): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('t.treeNumber, d.id AS descriptor_id, t.id')
            ->from(MeshTree::class, 't')
            ->join('t.descriptor', 'd')
            ->orderBy('t.id');
        return $qb->getQuery()->getScalarResult();
    }

    public function exportMeshConcepts(): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('c.id, c.name, c.preferred, c.scopeNote, c.casn1Name')
            ->from(MeshConcept::class, 'c')
            ->orderBy('c.id');
        return $qb->getQuery()->getScalarResult();
    }

    public function exportMeshTerms(): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('t.meshTermUid, t.name, t.lexicalTag, t.conceptPreferred, t.recordPreferred, t.permuted, t.id')
            ->from(MeshTerm::class, 't')
            ->orderBy('t.id');
        return $qb->getQuery()->getScalarResult();
    }

    public function exportMeshQualifiers(): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('q.id, q.name')
            ->from(MeshQualifier::class, 'q')
            ->orderBy('q.id')
            ->addOrderBy('q.name');
        return $qb->getQuery()->getScalarResult();
    }

    public function exportMeshPreviousIndexings(): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('d.id AS descriptor_id, p.previousIndexing, p.id')
            ->from(MeshPreviousIndexing::class, 'p')
            ->join('p.descriptor', 'd')
            ->orderBy('p.id');
        return $qb->getQuery()->getScalarResult();
    }

    public function exportMeshConceptTerms(): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('c.id AS concept_id, t.id AS term_id')
            ->from(MeshConcept::class, 'c')
            ->join('c.terms', 't')
            ->orderBy('t.id')
            ->addOrderBy('c.id');
        return $qb->getQuery()->getScalarResult();
    }

    public function exportMeshDescriptorQualifiers(): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('d.id AS descriptor_id, q.id AS qualifier_id')
            ->from(MeshDescriptor::class, 'd')
            ->join('d.qualifiers', 'q')
            ->orderBy('d.id')
            ->addOrderBy('q.id');
        return $qb->getQuery()->getScalarResult();
    }

    public function exportMeshDescriptorConcepts(): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('c.id AS concept_id, d.id AS descriptor_id')
            ->from(MeshDescriptor::class, 'd')
            ->join('d.concepts', 'c')
            ->orderBy('c.id')
            ->addOrderBy('d.id');
        return $qb->getQuery()->getScalarResult();
    }
}
