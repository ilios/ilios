<?php

declare(strict_types=1);

namespace App\Repository;

use App\Classes\IndexableCourse;
use App\Classes\IndexableSession;
use App\Entity\Course;
use App\Entity\Session;
use App\Service\DTOCacheManager;
use App\Traits\ManagerRepository;
use DateTime;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use App\Entity\DTO\CourseDTO;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

use function array_values;
use function array_keys;

class CourseRepository extends ServiceEntityRepository implements
    DTORepositoryInterface,
    RepositoryInterface
{
    use ManagerRepository;

    public function __construct(
        ManagerRegistry $registry,
        protected DTOCacheManager $cacheManager,
    ) {
        parent::__construct($registry, Course::class);
    }

    protected function findIdsBy(
        array $criteria,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null,
    ): array {
        if (array_key_exists('startDate', $criteria)) {
            $criteria['startDate'] = new DateTime($criteria['startDate']);
        }
        if (array_key_exists('endDate', $criteria)) {
            $criteria['endDate'] = new DateTime($criteria['endDate']);
        }

        return $this->doFindIdsBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Find by a string query
     */
    public function findDTOsByQ(
        string $q,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null,
    ): array {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->addSelect('x')->from(Course::class, 'x');

        $terms = explode(' ', $q);
        $terms = array_filter($terms, 'strlen');
        if (empty($terms)) {
            return [];
        }

        foreach ($terms as $key => $term) {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('x.title', "?{$key}"),
                $qb->expr()->like('x.year', "?{$key}"),
                $qb->expr()->like('x.externalId', "?{$key}"),
            ))
                ->setParameter($key, '%' . $term . '%');
        }

        if (empty($orderBy)) {
            $orderBy = ['id' => 'ASC'];
        }

        foreach ($orderBy as $sort => $order) {
            $qb->addOrderBy('x.' . $sort, $order);
        }

        if ($offset) {
            $qb->setFirstResult($offset);
        }

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $this->createCourseDTOS($qb->getQuery());
    }

    public function hydrateDTOsFromIds(array $ids): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder()->select('x')->distinct()->from(Course::class, 'x');
        $qb->where($qb->expr()->in('x.id', ':ids'));
        $qb->setParameter(':ids', $ids);

        return $this->createCourseDTOS($qb->getQuery());
    }

    protected function createCourseDTOS(AbstractQuery $query): array
    {
        $dtos = [];
        foreach ($query->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $dtos[$arr['id']] = new CourseDTO(
                $arr['id'],
                $arr['title'],
                $arr['level'],
                $arr['year'],
                $arr['startDate'],
                $arr['endDate'],
                $arr['externalId'],
                $arr['locked'],
                $arr['archived'],
                $arr['publishedAsTbd'],
                $arr['published']
            );
        }
        return $this->attachAssociationsToDTOs($dtos);
    }

    public function getYears(): array
    {
        $dql = 'SELECT DISTINCT c.year FROM App\Entity\Course c ORDER BY c.year ASC';
        $results = $this->getEntityManager()->createQuery($dql)->getArrayResult();

        $return = [];
        foreach ($results as $arr) {
            $return[] = $arr['year'];
        }

        return $return;
    }

    /**
     * Get all the IDs
     */
    public function getIds(): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->addSelect('x.id')->from(Course::class, 'x');

        return array_map(fn(array $arr) => $arr['id'], $qb->getQuery()->getScalarResult());
    }

    /**
     * Get Ids for all courses with at least one session
     */
    public function getIdsForCoursesWithSessions(): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('c.id')->from(Session::class, 'x')
            ->join('x.course', 'c')
            ->distinct();

        return array_map(fn(array $arr) => $arr['id'], $qb->getQuery()->getScalarResult());
    }

    /**
     * Finds all courses associated with a given user.
     * A user can be associated as either course director, learner or instructor with a given course.
     *
     * @param array|null $orderBy
     * @return CourseDTO[]
     * @throws Exception
     */
    public function findByUserId(
        int $userId,
        array $criteria,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null
    ): array {
        $rows = $this->findMyCourses($userId, $criteria, $orderBy, $limit, $offset);

        $courseDTOs = [];
        foreach ($rows as $arr) {
            $courseDTOs[$arr['course_id']] = new CourseDTO(
                (int) $arr['course_id'],
                $arr['title'],
                (int) $arr['course_level'],
                (int) $arr['year'],
                new DateTime($arr['start_date']),
                new DateTime($arr['end_date']),
                $arr['external_id'],
                (bool) $arr['locked'],
                (bool) $arr['archived'],
                (bool) $arr['published_as_tbd'],
                (bool) $arr['published']
            );
        }

        return $this->attachAssociationsToDTOs($courseDTOs);
    }

    protected function findMyCourses(
        int $userId,
        array $criteria,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null
    ): array {
        $meta = $this->getEntityManager()->getClassMetadata(Course::class);

        if (empty($orderBy)) {
            $orderBy = ['id' => 'ASC'];
        }

        $sql = <<<EOL
SELECT * FROM (
  SELECT c.* FROM course c
    JOIN course_director cd ON cd.course_id = c.course_id
    JOIN user u ON u.user_id = cd.user_id
    WHERE u.user_id = :user_id
  UNION
  SELECT c.* FROM course c
    JOIN `session` s ON s.course_id = c.course_id
    JOIN offering o ON o.session_id = s.session_id
    JOIN offering_x_learner oxl ON oxl.offering_id = o.offering_id
    JOIN user u ON u.user_id = oxl.user_id
    WHERE u.user_id = :user_id
  UNION
  SELECT c.* FROM course c
    JOIN `session` s ON s.course_id = c.course_id
    JOIN offering o ON o.session_id = s.session_id
    JOIN offering_x_group oxg ON oxg.offering_id = o.offering_id
    JOIN `group` g ON g.group_id = oxg.group_id
    JOIN group_x_user gxu ON gxu.group_id = g.group_id
    JOIN user u ON u.user_id = gxu.user_id
    WHERE u.user_id = :user_id
  UNION
  SELECT c.* FROM course c
    JOIN `session` s ON s.course_id = c.course_id
    JOIN ilm_session_facet ilm ON ilm.session_id = s.session_id
    JOIN ilm_session_facet_x_learner ilmxl ON ilmxl.ilm_session_facet_id = ilm.ilm_session_facet_id
    JOIN user u ON u.user_id = ilmxl.user_id
    WHERE u.user_id = :user_id
  UNION
  SELECT c.* FROM course c
    JOIN `session` s ON s.course_id = c.course_id
    JOIN ilm_session_facet ilm ON ilm.session_id = s.session_id
    JOIN ilm_session_facet_x_group ilmxg ON ilmxg.ilm_session_facet_id = ilm.ilm_session_facet_id
    JOIN `group` g ON g.group_id = ilmxg.group_id
    JOIN group_x_user gxu ON gxu.group_id = g.group_id
    JOIN user u ON u.user_id = gxu.user_id
    WHERE u.user_id = :user_id
  UNION
  SELECT c.* FROM course c
    JOIN `session` s ON s.course_id = c.course_id
    JOIN offering o ON o.session_id = s.session_id
    JOIN offering_x_instructor oxi ON oxi.offering_id = o.offering_id
    JOIN user u ON u.user_id = oxi.user_id
    WHERE u.user_id = :user_id
  UNION
  SELECT c.* FROM course c
    JOIN `session` s ON s.course_id = c.course_id
    JOIN offering o ON o.session_id = s.session_id
    JOIN offering_x_instructor_group oxig ON oxig.offering_id = o.offering_id
    JOIN instructor_group ig ON ig.instructor_group_id = oxig.instructor_group_id
    JOIN instructor_group_x_user igxu ON igxu.instructor_group_id = ig.instructor_group_id
    JOIN user u ON u.user_id = igxu.user_id
    WHERE u.user_id = :user_id
  UNION
  SELECT c.* FROM course c
    JOIN `session` s ON s.course_id = c.course_id
    JOIN ilm_session_facet ilm ON ilm.session_id = s.session_id
    JOIN ilm_session_facet_x_instructor ilmxi ON ilmxi.ilm_session_facet_id = ilm.ilm_session_facet_id
    JOIN user u ON u.user_id = ilmxi.user_id
    WHERE u.user_id = :user_id
  UNION
  SELECT c.* FROM course c
    JOIN `session` s ON s.course_id = c.course_id
    JOIN ilm_session_facet ilm ON ilm.session_id = s.session_id
    JOIN ilm_session_facet_x_instructor_group ilmxig ON ilmxig.ilm_session_facet_id = ilm.ilm_session_facet_id
    JOIN instructor_group ig ON ig.instructor_group_id = ilmxig.instructor_group_id
    JOIN instructor_group_x_user igxu ON igxu.instructor_group_id = ig.instructor_group_id
    JOIN user u ON u.user_id = igxu.user_id
    WHERE u.user_id = :user_id
  UNION
  SELECT c.* FROM course c
    JOIN course_administrator ca ON c.course_id = ca.course_id
    JOIN user u ON u.user_id = ca.user_id
    WHERE u.user_id = :user_id
) AS my_courses
EOL;

        $params = [];
        $i = 0;
        $sqlFragments = [];
        foreach ($criteria as $name => $value) {
            $i++;
            if (!$meta->hasField($name)) {
                throw new Exception(sprintf('"%s" is not a property of the Course entity.', $name));
            }

            $column = $meta->getColumnName($name);
            $label = ':param' . $i;
            if (is_array($value)) {
                $labels = array_map(fn($j) => "{$label}_{$j}", range(0, count($value) - 1));
                $params[$name] = $labels;
                $sqlFragments[] = "{$column} IN (" . implode(', ', $labels) . ")";
            } else {
                $params[$name] = $label;
                $sqlFragments[] = "{$column} = {$label}";
            }
        }
        if ($sqlFragments !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $sqlFragments);
        }

        $sqlFragments = [];
        foreach ($orderBy as $sort => $order) {
            if (!$meta->hasField($sort)) {
                throw new Exception(sprintf('"%s" is not a property of the Course entity.', $sort));
            }
            $column = $meta->getColumnName($sort);
            $sqlFragments[] = "{$column} " . ('desc' === strtolower($order) ? 'DESC' : 'ASC');
        }
        $sql .= ' ORDER BY ';
        $sql .= implode(', ', $sqlFragments);

        if (isset($limit)) {
            $sql .= ' LIMIT :limit';
        }

        if (isset($offset)) {
            $sql .= ' OFFSET :offset';
        }

        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":user_id", (int) $userId);
        foreach ($params as $field => $label) {
            $value = $criteria[$field];
            if (is_array($label)) {
                $arr = array_combine($label, $value);
                foreach ($arr as $label => $value) {
                    $stmt->bindValue($label, $value);
                }
            } else {
                $stmt->bindValue($label, $value);
            }
        }

        if (isset($limit)) {
            $stmt->bindValue(":limit", (int) $limit);
        }
        if (isset($offset)) {
            $stmt->bindValue(":offset", (int) $offset);
        }
        $result = $stmt->executeQuery();
        $rows = $result->fetchAllAssociative();
        $result->free();
        return $rows;
    }

    protected function attachAssociationsToDTOs(array $dtos): array
    {
        if ($dtos === []) {
            return $dtos;
        }
        $courseIds = array_keys($dtos);

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('s.id as schoolId, cl.id as clerkshipTypeId, a.id as ancestorId, c.id as courseId')
            ->from(Course::class, 'c')
            ->join('c.school', 's')
            ->leftJoin('c.clerkshipType', 'cl')
            ->leftJoin('c.ancestor', 'a')
            ->where($qb->expr()->in('c.id', ':courseIds'))
            ->setParameter('courseIds', $courseIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $dtos[$arr['courseId']]->school = (int)$arr['schoolId'];
            $dtos[$arr['courseId']]->clerkshipType =
                $arr['clerkshipTypeId'] ? (int)$arr['clerkshipTypeId'] : null;
            $dtos[$arr['courseId']]->ancestor = $arr['ancestorId'] ? (int)$arr['ancestorId'] : null;
        }

        $dtos = $this->attachRelatedToDtos(
            $dtos,
            [
                'directors',
                'administrators',
                'studentAdvisors',
                'cohorts',
                'terms',
                'courseObjectives',
                'meshDescriptors',
                'learningMaterials',
                'sessions',
                'descendants',
            ],
        );

        return array_values($dtos);
    }

    protected function attachCriteriaToQueryBuilder(
        QueryBuilder $qb,
        array $criteria,
        ?array $orderBy,
        ?int $limit,
        ?int $offset
    ): void {
        if (array_key_exists('sessions', $criteria)) {
            $ids = is_array($criteria['sessions']) ? $criteria['sessions'] : [$criteria['sessions']];
            $qb->join('x.sessions', 'se_session');
            $qb->andWhere($qb->expr()->in('se_session.id', ':sessions'));
            $qb->setParameter(':sessions', $ids);
        }

        if (array_key_exists('terms', $criteria)) {
            $ids = is_array($criteria['terms']) ? $criteria['terms'] : [$criteria['terms']];
            $qb->leftJoin('x.terms', 't_term1');
            $qb->leftJoin('x.sessions', 't_session');
            $qb->leftJoin('t_session.terms', 't_term2');
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->in('t_term1.id', ':terms'),
                $qb->expr()->in('t_term2.id', ':terms')
            ));
            $qb->setParameter(':terms', $ids);
        }

        if (array_key_exists('programs', $criteria)) {
            $ids = is_array($criteria['programs']) ? $criteria['programs'] : [$criteria['programs']];
            $qb->join('x.cohorts', 'p_cohort');
            $qb->join('p_cohort.programYear', 'p_programYear');
            $qb->join('p_programYear.program', 'p_program');
            $qb->andWhere($qb->expr()->in('p_program.id', ':programs'));
            $qb->setParameter(':programs', $ids);
        }

        if (array_key_exists('programYears', $criteria)) {
            $ids = is_array($criteria['programYears']) ? $criteria['programYears'] : [$criteria['programYears']];
            $qb->join('x.cohorts', 'py_cohort');
            $qb->join('py_cohort.programYear', 'py_programYear');
            $qb->andWhere($qb->expr()->in('py_programYear.id', ':programYears'));
            $qb->setParameter(':programYears', $ids);
        }

        if (array_key_exists('instructors', $criteria)) {
            $ids = is_array($criteria['instructors']) ? $criteria['instructors'] : [$criteria['instructors']];
            $qb->leftJoin('x.sessions', 'i_session');
            $qb->leftJoin('x.directors', 'i_director');
            $qb->leftJoin('i_session.offerings', 'i_offering');
            $qb->leftJoin('i_offering.instructors', 'i_user');
            $qb->leftJoin('i_offering.instructorGroups', 'i_insGroup');
            $qb->leftJoin('i_insGroup.users', 'i_groupUser');
            $qb->leftJoin('i_session.ilmSession', 'i_ilmSession');
            $qb->leftJoin('i_ilmSession.instructors', 'i_ilmInstructor');
            $qb->leftJoin('i_ilmSession.instructorGroups', 'i_ilmInsGroup');
            $qb->leftJoin('i_ilmInsGroup.users', 'i_ilmInsGroupUser');
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->in('i_director.id', ':users'),
                $qb->expr()->in('i_user.id', ':users'),
                $qb->expr()->in('i_groupUser.id', ':users'),
                $qb->expr()->in('i_ilmInstructor.id', ':users'),
                $qb->expr()->in('i_ilmInsGroupUser.id', ':users')
            ));
            $qb->setParameter(':users', $ids);
        }

        if (array_key_exists('instructorGroups', $criteria)) {
            $ids = is_array($criteria['instructorGroups']) ?
                $criteria['instructorGroups'] : [$criteria['instructorGroups']];
            $qb->leftJoin('x.sessions', 'ig_session');
            $qb->leftJoin('ig_session.offerings', 'ig_offering');
            $qb->leftJoin('ig_offering.instructorGroups', 'ig_igroup');
            $qb->leftJoin('ig_session.ilmSession', 'ig_ilmSession');
            $qb->leftJoin('ig_ilmSession.instructorGroups', 'ig_ilmInsGroup');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('ig_igroup.id', ':igroups'),
                    $qb->expr()->in('ig_ilmInsGroup.id', ':igroups')
                )
            );
            $qb->setParameter(':igroups', $ids);
        }

        if (array_key_exists('learningMaterials', $criteria)) {
            $ids = is_array($criteria['learningMaterials']) ?
                $criteria['learningMaterials'] : [$criteria['learningMaterials']];
            $qb->leftJoin('x.learningMaterials', 'lm_clm');
            $qb->leftJoin('lm_clm.learningMaterial', 'lm_lm');
            $qb->leftJoin('x.sessions', 'lm_session');
            $qb->leftJoin('lm_session.learningMaterials', 'lm_slm');
            $qb->leftJoin('lm_slm.learningMaterial', 'lm_lm2');
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->in('lm_lm.id', ':lms'),
                $qb->expr()->in('lm_lm2.id', ':lms')
            ));
            $qb->setParameter(':lms', $ids);
        }

        if (array_key_exists('competencies', $criteria)) {
            $ids = is_array($criteria['competencies']) ? $criteria['competencies'] : [$criteria['competencies']];
            $qb->join('x.courseObjectives', 'c_course_objective');
            $qb->join('c_course_objective.programYearObjectives', 'c_program_year_objective');
            $qb->leftJoin('c_program_year_objective.competency', 'c_competency');
            $qb->leftJoin('c_competency.parent', 'c_competency2');
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->in('c_competency.id', ':competencies'),
                $qb->expr()->in('c_competency2.id', ':competencies')
            ));
            $qb->setParameter(':competencies', $ids);
        }

        if (array_key_exists('meshDescriptors', $criteria)) {
            $ids = is_array($criteria['meshDescriptors']) ?
                $criteria['meshDescriptors'] : [$criteria['meshDescriptors']];
            $qb->leftJoin('x.meshDescriptors', 'm_meshDescriptor');
            $qb->leftJoin('x.sessions', 'm_session');
            $qb->leftJoin('m_session.meshDescriptors', 'm_sessMeshDescriptor');
            $qb->leftJoin('x.courseObjectives', 'm_cObjective');
            $qb->leftJoin('m_cObjective.meshDescriptors', 'm_cObjectiveMeshDescriptor');
            $qb->leftJoin('m_session.sessionObjectives', 'm_sObjective');
            $qb->leftJoin('m_sObjective.meshDescriptors', 'm_sObjectiveMeshDescriptors');
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->in('m_meshDescriptor.id', ':meshDescriptors'),
                $qb->expr()->in('m_sessMeshDescriptor.id', ':meshDescriptors'),
                $qb->expr()->in('m_cObjectiveMeshDescriptor.id', ':meshDescriptors'),
                $qb->expr()->in('m_sObjectiveMeshDescriptors.id', ':meshDescriptors')
            ));
            $qb->setParameter(':meshDescriptors', $ids);
        }

        if (array_key_exists('schools', $criteria)) {
            $ids = is_array($criteria['schools']) ? $criteria['schools'] : [$criteria['schools']];
            $qb->join('x.school', 'sc_school');
            $qb->andWhere($qb->expr()->in('sc_school.id', ':schools'));
            $qb->setParameter(':schools', $ids);
        }

        if (array_key_exists('ancestors', $criteria)) {
            $ids = is_array($criteria['ancestors']) ? $criteria['ancestors'] : [$criteria['ancestors']];
            $qb->join('x.ancestor', 'anc_course');
            $qb->andWhere($qb->expr()->in('anc_course.id', ':ancestors'));
            $qb->setParameter(':ancestors', $ids);
        }

        if (array_key_exists('academicYears', $criteria)) {
            $ids = is_array($criteria['academicYears']) ? $criteria['academicYears'] : [$criteria['academicYears']];
            $qb->andWhere($qb->expr()->in('x.year', ':academicYears'));
            $qb->setParameter(':academicYears', $ids);
        }

        //cleanup all the possible relationship filters
        unset($criteria['schools']);
        unset($criteria['sessions']);
        unset($criteria['terms']);
        unset($criteria['programs']);
        unset($criteria['programYears']);
        unset($criteria['instructors']);
        unset($criteria['instructorGroups']);
        unset($criteria['learningMaterials']);
        unset($criteria['competencies']);
        unset($criteria['meshDescriptors']);
        unset($criteria['ancestors']);
        unset($criteria['academicYears']);

        $this->attachClosingCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
    }

    /**
     * Create course index objects for a set of courses
     *
     */
    public function getCourseIndexesFor(array $courseIds): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(
            'c.id AS id, c.title AS title, c.level AS level, c.year AS year, ' .
            'c.startDate AS startDate, c.endDate AS endDate, c.externalId AS externalId, ' .
            'c.locked AS locked, c.archived AS archived, c.publishedAsTbd AS publishedAsTbd, ' .
            'c.published AS published, s.title as school, s.id as schoolId, cl.title as clerkshipType'
        )
            ->from(Course::class, 'c')
            ->join('c.school', 's')
            ->leftJoin('c.clerkshipType', 'cl')
            ->where($qb->expr()->in('c.id', ':courseIds'))
            ->setParameter('courseIds', $courseIds);

        /** @var IndexableCourse[] $indexableCourses */
        $indexableCourses = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $index = new IndexableCourse();
            $index->courseDTO = new CourseDTO(
                $arr['id'],
                $arr['title'],
                $arr['level'],
                $arr['year'],
                $arr['startDate'],
                $arr['endDate'],
                $arr['externalId'],
                $arr['locked'],
                $arr['archived'],
                $arr['publishedAsTbd'],
                $arr['published']
            );
            $index->school = $arr['school'];
            $index->schoolId = $arr['schoolId'];
            $index->clerkshipType = $arr['clerkshipType'] ?: null;

            $indexableCourses[$arr['id']] = $index;
        }

        $directors = $this->joinResults(
            Course::class,
            'directors',
            "CONCAT(r.firstName, ' ', r.lastName, ' ', r.displayName) AS name",
            $courseIds
        );
        foreach ($directors as $courseId => $arr) {
            $indexableCourses[$courseId]->directors[] = $arr[0]['name'];
        }

        $administrators = $this->joinResults(
            Course::class,
            'administrators',
            "CONCAT(r.firstName, ' ', r.lastName, ' ', r.displayName) AS name",
            $courseIds
        );
        foreach ($administrators as $courseId => $arr) {
            $indexableCourses[$courseId]->administrators[] = $arr[0]['name'];
        }

        $terms = $this->joinResults(
            Course::class,
            'terms',
            "r.title",
            $courseIds
        );
        foreach ($terms as $courseId => $arr) {
            $indexableCourses[$courseId]->terms[] = $arr[0]['title'];
        }

        $objectives = $this->joinObjectiveResults(
            Course::class,
            'courseObjectives',
            'o.title',
            $courseIds
        );
        foreach ($objectives as $courseId => $arr) {
            $indexableCourses[$courseId]->objectives[] = $arr[0]['title'];
        }

        $meshDescriptors = $this->joinResults(
            Course::class,
            'meshDescriptors',
            "r.id, r.name, r.annotation",
            $courseIds
        );
        foreach ($meshDescriptors as $courseId => $courseDescriptors) {
            foreach ($courseDescriptors as $arr) {
                $indexableCourses[$courseId]->meshDescriptorIds[] = $arr['id'];
                $indexableCourses[$courseId]->meshDescriptorNames[] = $arr['name'];
                $indexableCourses[$courseId]->meshDescriptorAnnotations[] = $arr['annotation'];
            }
        }

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select("c.id AS courseId, l.id as learningMaterialId, l.relativePath, " .
                "l.title, l.description, l.citation")
            ->from(Course::class, 'c')
            ->join("c.learningMaterials", 'clm')
            ->join("clm.learningMaterial", 'l')
            ->where($qb->expr()->in('c.id', ':ids'))
            ->setParameter('ids', $courseIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $indexableCourses[$arr['courseId']]->learningMaterialTitles[] = $arr['title'];
            $indexableCourses[$arr['courseId']]->learningMaterialDescriptions[] = $arr['description'];
            $indexableCourses[$arr['courseId']]->learningMaterialCitations[] = $arr['citation'];
            if ($arr['relativePath']) {
                $indexableCourses[$arr['courseId']]->fileLearningMaterialIds[] = $arr['learningMaterialId'];
            }
        }

        $arr = $this->joinResults(
            Course::class,
            'sessions',
            "r.id AS sessionId",
            $courseIds
        );

        $sessionIds = array_reduce(
            $arr,
            fn(array $carry, array $item) => array_merge($carry, array_column($item, 'sessionId')),
            []
        );

        $chunks = array_chunk(array_unique($sessionIds), 500);
        foreach ($chunks as $ids) {
            $indexableSessions = $this->sessionDataForIndex($ids);
            foreach ($indexableSessions as $session) {
                $indexableCourses[$session->courseId]->sessions[] = $session;
            }
        }

        return array_values($indexableCourses);
    }

    protected function joinResults(string $from, string $rel, string $select, array $ids): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select("f.id AS fromId, {$select}")->from($from, 'f')
            ->join("f.{$rel}", 'r')
            ->where($qb->expr()->in('f.id', ':ids'))
            ->setParameter('ids', $ids);

        $rhett = [];
        foreach ($qb->getQuery()->getResult() as $arr) {
            $rhett[$arr['fromId']][] = $arr;
        }

        return $rhett;
    }

    protected function joinObjectiveResults(string $from, string $rel, string $select, array $ids): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select("f.id AS fromId, {$select}")->from($from, 'f')
            ->join("f.{$rel}", 'o')
            ->where($qb->expr()->in('f.id', ':ids'))
            ->setParameter('ids', $ids);

        $rhett = [];
        foreach ($qb->getQuery()->getResult() as $arr) {
            $rhett[$arr['fromId']][] = $arr;
        }

        return $rhett;
    }

    protected function sessionDataForIndex(array $sessionIds): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(
            's.id AS sessionId, s.title AS title, s.description AS description, c.id as courseId, ' .
            'st.title AS sessionType'
        )
            ->from(Session::class, 's')
            ->join('s.course', 'c')
            ->leftJoin('s.sessionType', 'st')
            ->where($qb->expr()->in('s.id', ':ids'))
            ->setParameter('ids', $sessionIds);

        /** @var IndexableSession[] $sessions */
        $sessions = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $session = new IndexableSession();
            $session->courseId = $arr['courseId'];
            $session->sessionId = $arr['sessionId'];
            $session->title = $arr['title'];
            $session->sessionType = $arr['sessionType'];
            $session->description = $arr['description'];

            $sessions[$arr['sessionId']] = $session;
        }

        $administrators = $this->joinResults(
            Session::class,
            'administrators',
            "CONCAT(r.firstName, ' ', r.lastName, ' ', r.displayName) AS name",
            $sessionIds
        );
        foreach ($administrators as $sessionId => $arr) {
            $sessions[$sessionId]->administrators[] = $arr[0]['name'];
        }

        $terms = $this->joinResults(
            Session::class,
            'terms',
            "r.title",
            $sessionIds
        );
        foreach ($terms as $sessionId => $arr) {
            $sessions[$sessionId]->terms[] = $arr[0]['title'];
        }

        $objectives = $this->joinObjectiveResults(
            Session::class,
            'sessionObjectives',
            'o.title',
            $sessionIds
        );
        foreach ($objectives as $sessionId => $arr) {
            $sessions[$sessionId]->objectives[] = $arr[0]['title'];
        }

        $meshDescriptors = $this->joinResults(
            Session::class,
            'meshDescriptors',
            "r.id, r.name, r.annotation",
            $sessionIds
        );
        foreach ($meshDescriptors as $sessionId => $sessionDescriptors) {
            foreach ($sessionDescriptors as $arr) {
                $sessions[$sessionId]->meshDescriptorIds[] = $arr['id'];
                $sessions[$sessionId]->meshDescriptorNames[] = $arr['name'];
                $sessions[$sessionId]->meshDescriptorAnnotations[] = $arr['annotation'];
            }
        }

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select("s.id AS sessionId, l.id as learningMaterialId, l.relativePath, " .
                "l.title, l.description, l.citation")
            ->from(Session::class, 's')
            ->join("s.learningMaterials", 'slm')
            ->join("slm.learningMaterial", 'l')
            ->where($qb->expr()->in('s.id', ':ids'))
            ->setParameter('ids', $sessionIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $sessions[$arr['sessionId']]->learningMaterialTitles[] = $arr['title'];
            $sessions[$arr['sessionId']]->learningMaterialDescriptions[] = $arr['description'];
            $sessions[$arr['sessionId']]->learningMaterialCitations[] = $arr['citation'];
            if ($arr['relativePath']) {
                $sessions[$arr['sessionId']]->fileLearningMaterialIds[] = $arr['learningMaterialId'];
            }
        }

        return array_values($sessions);
    }
}
