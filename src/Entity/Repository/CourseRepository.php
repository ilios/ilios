<?php

namespace App\Entity\Repository;

use App\Classes\IndexableCourse;
use App\Entity\Course;
use App\Entity\Session;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\QueryBuilder;
use App\Entity\CourseInterface;
use App\Entity\UserInterface;
use App\Entity\DTO\CourseDTO;

class CourseRepository extends EntityRepository implements DTORepositoryInterface
{
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
        $qb->select('DISTINCT c')->from('App\Entity\Course', 'c');

        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        return $qb->getQuery()->getResult();
    }

    /**
     * Find and hydrate as DTOs
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     *
     * @return array
     */
    public function findDTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder()->select('c')->distinct()->from('App\Entity\Course', 'c');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        $courseDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $courseDTOs[$arr['id']] = new CourseDTO(
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
        $courseIds = array_keys($courseDTOs);

        $qb = $this->_em->createQueryBuilder()
            ->select('s.id as schoolId, cl.id as clerkshipTypeId, a.id as ancestorId, c.id as courseId')
            ->from('App\Entity\Course', 'c')
            ->join('c.school', 's')
            ->leftJoin('c.clerkshipType', 'cl')
            ->leftJoin('c.ancestor', 'a')
            ->where($qb->expr()->in('c.id', ':courseIds'))
            ->setParameter('courseIds', $courseIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $courseDTOs[$arr['courseId']]->school = (int)$arr['schoolId'];
            $courseDTOs[$arr['courseId']]->clerkshipType = $arr['clerkshipTypeId'] ? (int)$arr['clerkshipTypeId'] : null;
            $courseDTOs[$arr['courseId']]->ancestor = $arr['ancestorId'] ? (int)$arr['ancestorId'] : null;
        }

        $related = [
            'directors',
            'administrators',
            'cohorts',
            'terms',
            'objectives',
            'meshDescriptors',
            'learningMaterials',
            'sessions',
            'descendants',
        ];

        foreach ($related as $rel) {
            $qb = $this->_em->createQueryBuilder()
                ->select('r.id as relId, c.id as courseId')->from('App\Entity\Course', 'c')
                ->join("c.{$rel}", 'r')
                ->where($qb->expr()->in('c.id', ':courseIds'))
                ->orderBy('relId')
                ->setParameter('courseIds', $courseIds);

            foreach ($qb->getQuery()->getResult() as $arr) {
                $courseDTOs[$arr['courseId']]->{$rel}[] = $arr['relId'];
            }
        }


        return array_values($courseDTOs);
    }

    /**
     * @return array
     */
    public function getYears()
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
     *
     * @return array
     */
    public function getIds()
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->addSelect('x.id')->from(Course::class, 'x');

        return array_map(function (array $arr) {
            return $arr['id'];
        }, $qb->getQuery()->getScalarResult());
    }

    /**
     * Checks if a given user is assigned as instructor to ILMs or offerings in a given course.
     *
     * @param int $userId
     * @param int $courseId
     * @return boolean TRUE if the user instructs at least one offering or ILM, FALSE otherwise.
     */
    public function isUserInstructingInCourse($userId, $courseId)
    {
        $sql = <<<EOL
SELECT
  oxi.user_id
FROM
  offering_x_instructor oxi
JOIN offering o ON o.offering_id = oxi.offering_id
JOIN session s ON s.session_id = o.session_id
WHERE
  oxi.user_id = :user_id
  AND s.course_id = :course_id

UNION

SELECT
  igxu.user_id
FROM 
  instructor_group_x_user igxu 
JOIN offering_x_instructor_group oxig ON oxig.instructor_group_id = igxu.instructor_group_id
JOIN offering o ON o.offering_id = oxig.offering_id
JOIN session s ON s.session_id = o.session_id
WHERE 
  igxu.user_id = :user_id
  AND s.course_id = :course_id

UNION

SELECT 
  ixi.user_id
FROM 
  ilm_session_facet_x_instructor ixi
JOIN ilm_session_facet i ON i.ilm_session_facet_id = ixi.ilm_session_facet_id
JOIN session s ON s.session_id = i.session_id
WHERE
  ixi.user_id = :user_id
  AND s.course_id = :course_id

UNION

SELECT
  igxu.user_id
FROM
  instructor_group_x_user igxu
JOIN ilm_session_facet_x_instructor_group ixig ON ixig.instructor_group_id = igxu.instructor_group_id
JOIN ilm_session_facet i ON i.ilm_session_facet_id = ixig.ilm_session_facet_id
JOIN session s ON s.session_id = i.session_id
WHERE
  igxu.user_id = :user_id
  AND s.course_id = :course_id
EOL;

        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue("user_id", $userId);
        $stmt->bindValue("course_id", $courseId);
        $stmt->execute();
        $rows = $stmt->fetchAll();
        $isInstructing = !empty($rows);
        $stmt->closeCursor();
        return $isInstructing;
    }

    /**
     * Finds all courses associated with a given user.
     * A user can be associated as either course director, learner or instructor with a given course.
     *
     * @param integer $user
     * @param array $criteria
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     * @return CourseInterface[]
     * @throws \Exception
     */
    public function findByUserId(
        $userId,
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    )
    {

        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addRootEntityFromClassMetadata('App\Entity\Course', 'c');
        $meta = $this->_em->getClassMetadata('App\Entity\Course');

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
                throw new \Exception(sprintf('"%s" is not a property of the Course entity.', $name));
            }

            $column = $meta->getColumnName($name);
            $label = 'param' . $i;
            $params[$name] = $label;
            if (is_array($value)) {
                $sqlFragments[] = "{$column} IN (:{$label})";
            } else {
                $sqlFragments[] = "{$column} = :{$label}";
            }
        }
        if (count($sqlFragments)) {
            $sql .= ' WHERE ' . implode(' AND ', $sqlFragments);
        }

        if (is_array($orderBy)) {
            $sqlFragments = [];
            foreach ($orderBy as $sort => $order) {
                if (!$meta->hasField($sort)) {
                    throw new \Exception(sprintf('"%s" is not a property of the Course entity.', $sort));
                }
                $column = $meta->getColumnName($sort);
                $sqlFragments[] = "{$column} " . ('desc' === strtolower($order) ? 'DESC' : 'ASC');
            }
            $sql .= ' ORDER BY ';
            $sql .= implode(', ', $sqlFragments);
        }

        if (isset($limit)) {
            $sql .= ' LIMIT :limit';
        }

        if (isset($offset)) {
            $sql .= ' OFFSET :offset';
        }

        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameter('user_id', $userId);
        foreach ($params as $field => $label) {
            $value = $criteria[$field];
            $query->setParameter($label, $value);
        }

        if (isset($limit)) {
            $query->setParameter('limit', (int)$limit);
        }
        if (isset($offset)) {
            $query->setParameter('offset', (int)$offset);
        }
        return $query->getResult();
    }


    /**
     * Custom findBy so we can filter by related entities
     *
     * @param QueryBuilder $qb
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return QueryBuilder
     */
    protected function attachCriteriaToQueryBuilder(QueryBuilder $qb, $criteria, $orderBy, $limit, $offset)
    {
        if (array_key_exists('sessions', $criteria)) {
            $ids = is_array($criteria['sessions']) ? $criteria['sessions'] : [$criteria['sessions']];
            $qb->join('c.sessions', 'se_session');
            $qb->andWhere($qb->expr()->in('se_session.id', ':sessions'));
            $qb->setParameter(':sessions', $ids);
        }

        if (array_key_exists('terms', $criteria)) {
            $ids = is_array($criteria['terms']) ? $criteria['terms'] : [$criteria['terms']];
            $qb->leftJoin('c.terms', 't_term1');
            $qb->leftJoin('c.sessions', 't_session');
            $qb->leftJoin('t_session.terms', 't_term2');
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->in('t_term1.id', ':terms'),
                $qb->expr()->in('t_term2.id', ':terms')
            ));
            $qb->setParameter(':terms', $ids);
        }

        if (array_key_exists('programs', $criteria)) {
            $ids = is_array($criteria['programs']) ? $criteria['programs'] : [$criteria['programs']];
            $qb->join('c.cohorts', 'p_cohort');
            $qb->join('p_cohort.programYear', 'p_programYear');
            $qb->join('p_programYear.program', 'p_program');
            $qb->andWhere($qb->expr()->in('p_program.id', ':programs'));
            $qb->setParameter(':programs', $ids);
        }

        if (array_key_exists('programYears', $criteria)) {
            $ids = is_array($criteria['programYears']) ? $criteria['programYears'] : [$criteria['programYears']];
            $qb->join('c.cohorts', 'py_cohort');
            $qb->join('py_cohort.programYear', 'py_programYear');
            $qb->andWhere($qb->expr()->in('py_programYear.id', ':programYears'));
            $qb->setParameter(':programYears', $ids);
        }

        if (array_key_exists('instructors', $criteria)) {
            $ids = is_array($criteria['instructors']) ? $criteria['instructors'] : [$criteria['instructors']];
            $qb->leftJoin('c.sessions', 'i_session');
            $qb->leftJoin('c.directors', 'i_director');
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
            $qb->leftJoin('c.sessions', 'ig_session');
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
            $qb->leftJoin('c.learningMaterials', 'lm_clm');
            $qb->leftJoin('lm_clm.learningMaterial', 'lm_lm');
            $qb->leftJoin('c.sessions', 'lm_session');
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
            $qb->join('c.objectives', 'c_course_objective');
            $qb->join('c_course_objective.parents', 'c_program_year_objective');
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
            $qb->leftJoin('c.meshDescriptors', 'm_meshDescriptor');
            $qb->leftJoin('c.sessions', 'm_session');
            $qb->leftJoin('m_session.meshDescriptors', 'm_sessMeshDescriptor');
            $qb->leftJoin('c.objectives', 'm_cObjective');
            $qb->leftJoin('m_cObjective.meshDescriptors', 'm_cObjectiveMeshDescriptor');
            $qb->leftJoin('m_session.objectives', 'm_sObjective');
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
            $qb->join('c.school', 'sc_school');
            $qb->andWhere($qb->expr()->in('sc_school.id', ':schools'));
            $qb->setParameter(':schools', $ids);
        }

        if (array_key_exists('ancestors', $criteria)) {
            $ids = is_array($criteria['ancestors']) ? $criteria['ancestors'] : [$criteria['ancestors']];
            $qb->join('c.ancestor', 'anc_course');
            $qb->andWhere($qb->expr()->in('anc_course.id', ':ancestors'));
            $qb->setParameter(':ancestors', $ids);
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

        if (count($criteria)) {
            foreach ($criteria as $key => $value) {
                $values = is_array($value) ? $value : [$value];
                $qb->andWhere($qb->expr()->in("c.{$key}", ":{$key}"));
                $qb->setParameter(":{$key}", $values);
            }
        }

        if (empty($orderBy)) {
            $orderBy = ['id' => 'ASC'];
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('c.' . $sort, $order);
            }
        }


        if ($offset) {
            $qb->setFirstResult($offset);
        }

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb;
    }

    /**
     * Create course index objects for a set of courses
     *
     * @param array $courseIds
     *
     * @return IndexableCourse[]
     */
    public function getCourseIndexesFor(array $courseIds)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select(
            'c.id AS id, c.title AS title, c.level AS level, c.year AS year, ' .
            'c.startDate AS startDate, c.endDate AS endDate, c.externalId AS externalId, ' .
            'c.locked AS locked, c.archived AS archived, c.publishedAsTbd AS publishedAsTbd, ' .
            'c.published AS published, s.title as school, cl.title as clerkshipType'
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
            $index->clerkshipType = $arr['clerkshipType'] ? $arr['clerkshipType'] : null;

            $indexableCourses[$arr['id']] = $index;
        }

        $directors = $this->joinResults(
            Course::class,
            'directors',
            "CONCAT(r.firstName, ' ', r.lastName, ' ', r.displayName) AS name",
            $courseIds
        );
        foreach ($directors as $courseId => $arr) {
            $indexableCourses[$courseId]->courseDirectors[] = $arr[0]['name'];
        }

        $administrators = $this->joinResults(
            Course::class,
            'administrators',
            "CONCAT(r.firstName, ' ', r.lastName, ' ', r.displayName) AS name",
            $courseIds
        );
        foreach ($administrators as $courseId => $arr) {
            $indexableCourses[$courseId]->courseAdministrators[] = $arr[0]['name'];
        }

        $terms = $this->joinResults(
            Course::class,
            'terms',
            "r.title",
            $courseIds
        );
        foreach ($terms as $courseId => $arr) {
            $indexableCourses[$courseId]->courseTerms[] = $arr[0]['title'];
        }

        $objectives = $this->joinResults(
            Course::class,
            'objectives',
            "r.title",
            $courseIds
        );
        foreach ($objectives as $courseId => $arr) {
            $indexableCourses[$courseId]->courseObjectives[] = $arr[0]['title'];
        }

        $meshDescriptors = $this->joinResults(
            Course::class,
            'meshDescriptors',
            "r.id, r.name, r.annotation",
            $courseIds
        );
        foreach ($meshDescriptors as $courseId => $arr) {
            $indexableCourses[$courseId]->courseMeshDescriptors[] = $arr[0]['id'];
        }

        $qb = $this->_em->createQueryBuilder()
            ->select("c.id AS courseId, CONCAT(l.title, ' ', l.description) AS txt")
            ->from(Course::class, 'c')
            ->join("c.learningMaterials", 'clm')
            ->join("clm.learningMaterial", 'l')
            ->where($qb->expr()->in('c.id', ':courseIds'))
            ->setParameter('courseIds', $courseIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $indexableCourses[$arr['courseId']]->courseLearningMaterials[] = $arr['txt'];
        }

        $arr = $this->joinResults(
            Course::class,
            'sessions',
            "r.id AS sessionId",
            $courseIds
        );

        $sessionIds = array_reduce($arr, function (array $carry, array $item) {
            return array_merge($carry, array_column($item, 'sessionId'));
        }, []);

        $chunks = array_chunk(array_unique($sessionIds), 500);
        foreach ($chunks as $ids) {
            $sessionBlocks = $this->sessionDataForIndex($ids);
            foreach ($sessionBlocks as $arr) {
                $indexableCourses[$arr['courseId']]->sessions[] = $arr;
            }
        }

        return array_values($indexableCourses);
    }

    /**
     * @param string $from
     * @param string $rel
     * @param string $select
     * @param array $ids
     *
     * @return array
     */
    protected function joinResults(string $from, string $rel, string $select, array $ids)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select("f.id AS fromId, ${select}")->from($from, 'f')
            ->join("f.{$rel}", 'r')
            ->where($qb->expr()->in('f.id', ':ids'))
            ->setParameter('ids', $ids);

        $rhett = [];
        foreach ($qb->getQuery()->getResult() as $arr) {
            $rhett[$arr['fromId']][] = $arr;
        }

        return $rhett;
    }

    /**
     * @param array $sessionIds
     *
     * @return array
     */
    protected function sessionDataForIndex(array $sessionIds)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select(
            's.id AS sessionId, s.title AS title, sd.description AS description, c.id as courseId, ' .
            'st.title AS sessionType'
        )
            ->from(Session::class, 's')
            ->join('s.course', 'c')
            ->leftJoin('s.sessionType', 'st')
            ->leftJoin('s.sessionDescription', 'sd')
            ->where($qb->expr()->in('s.id', ':ids'))
            ->setParameter('ids', $sessionIds);

        $sessions = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $sessions[$arr['sessionId']] = $arr;
            $sessions[$arr['sessionId']]['administrators'] = [];
            $sessions[$arr['sessionId']]['terms'] = [];
            $sessions[$arr['sessionId']]['objectives'] = [];
            $sessions[$arr['sessionId']]['meshDescriptors'] = [];
            $sessions[$arr['sessionId']]['learningMaterials'] = [];
        }

        $administrators = $this->joinResults(
            Session::class,
            'administrators',
            "CONCAT(r.firstName, ' ', r.lastName, ' ', r.displayName) AS name",
            $sessionIds
        );
        foreach ($administrators as $sessionId => $arr) {
            $sessions[$sessionId]['administrators'][] = $arr[0]['name'];
        }

        $terms = $this->joinResults(
            Session::class,
            'terms',
            "r.title",
            $sessionIds
        );
        foreach ($terms as $sessionId => $arr) {
            $sessions[$sessionId]['terms'][] = $arr[0]['title'];
        }

        $objectives = $this->joinResults(
            Session::class,
            'objectives',
            "r.title",
            $sessionIds
        );
        foreach ($objectives as $sessionId => $arr) {
            $sessions[$sessionId]['objectives'][] = $arr[0]['title'];
        }

        $meshDescriptors = $this->joinResults(
            Session::class,
            'meshDescriptors',
            "r.id, r.name, r.annotation",
            $sessionIds
        );
        foreach ($meshDescriptors as $sessionId => $arr) {
            $sessions[$sessionId]['meshDescriptors'][] = $arr[0]['id'];
        }

        $qb = $this->_em->createQueryBuilder()
            ->select("s.id AS sessionId, CONCAT(l.title, ' ', l.description) AS txt")
            ->from(Session::class, 's')
            ->join("s.learningMaterials", 'slm')
            ->join("slm.learningMaterial", 'l')
            ->where($qb->expr()->in('s.id', ':ids'))
            ->setParameter('ids', $sessionIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $sessions[$arr['sessionId']]['learningMaterials'][] = $arr['txt'];
        }

        return array_values($sessions);
    }
}
