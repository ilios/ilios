<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Ilios\CoreBundle\Entity\DTO\SessionDTO;

class SessionRepository extends EntityRepository implements DTORepositoryInterface
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

        $qb->select('DISTINCT s')->from('IliosCoreBundle:Session', 's');

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
     * @return SessionDTO[]
     */
    public function findDTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder()->select('s')->distinct()->from('IliosCoreBundle:Session', 's');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        $sessionDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $sessionDTOs[$arr['id']] = new SessionDTO(
                $arr['id'],
                $arr['title'],
                $arr['attireRequired'],
                $arr['equipmentRequired'],
                $arr['supplemental'],
                $arr['attendanceRequired'],
                $arr['publishedAsTbd'],
                $arr['published'],
                $arr['updatedAt']
            );
        }
        $sessionIds = array_keys($sessionDTOs);

        $qb = $this->_em->createQueryBuilder()
            ->select('s.id AS sessionId, c.id AS courseId, st.id AS sessionTypeId, ilm.id AS ilmId, sd.id AS descId')
            ->from('IliosCoreBundle:Session', 's')
            ->join('s.course', 'c')
            ->join('s.sessionType', 'st')
            ->leftJoin('s.ilmSession', 'ilm')
            ->leftJoin('s.sessionDescription', 'sd')
            ->where($qb->expr()->in('s.id', ':sessionIds'))
            ->setParameter('sessionIds', $sessionIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $sessionDTOs[$arr['sessionId']]->course = $arr['courseId'];
            $sessionDTOs[$arr['sessionId']]->sessionType = $arr['sessionTypeId'];
            $sessionDTOs[$arr['sessionId']]->ilmSession = $arr['ilmId'] ? $arr['ilmId'] : null;
            $sessionDTOs[$arr['sessionId']]->sessionDescription = $arr['descId'] ? $arr['descId'] : null;
        }

        $related = [
            'terms',
            'objectives',
            'meshDescriptors',
            'learningMaterials',
            'offerings',
            'administrators'
        ];

        foreach ($related as $rel) {
            $qb = $this->_em->createQueryBuilder()
                ->select('r.id as relId, s.id AS sessionId')->from('IliosCoreBundle:Session', 's')
                ->join("s.{$rel}", 'r')
                ->where($qb->expr()->in('s.id', ':sessionIds'))
                ->orderBy('relId')
                ->setParameter('sessionIds', $sessionIds);

            foreach ($qb->getQuery()->getResult() as $arr) {
                $sessionDTOs[$arr['sessionId']]->{$rel}[] = $arr['relId'];
            }
        }

        return array_values($sessionDTOs);
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
        if (array_key_exists('terms', $criteria)) {
            $ids = is_array($criteria['terms']) ? $criteria['terms'] : [$criteria['terms']];
            $qb->join('s.terms', 'term');
            $qb->andWhere($qb->expr()->in('term.id', ':terms'));
            $qb->setParameter(':terms', $ids);
        }
        if (array_key_exists('instructors', $criteria)) {
            $ids = is_array($criteria['instructors']) ? $criteria['instructors'] : [$criteria['instructors']];
            $qb->leftJoin('s.offerings', 'i_offering');
            $qb->leftJoin('i_offering.instructors', 'i_user');
            $qb->leftJoin('i_offering.instructorGroups', 'i_insGroup');
            $qb->leftJoin('i_insGroup.users', 'i_groupUser');
            $qb->leftJoin('s.ilmSession', 'i_ilmSession');
            $qb->leftJoin('i_ilmSession.instructors', 'i_ilmInstructor');
            $qb->leftJoin('i_ilmSession.instructorGroups', 'i_ilmInsGroup');
            $qb->leftJoin('i_ilmInsGroup.users', 'i_ilmInsGroupUser');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('i_user.id', ':users'),
                    $qb->expr()->in('i_groupUser.id', ':users'),
                    $qb->expr()->in('i_ilmInstructor.id', ':users'),
                    $qb->expr()->in('i_ilmInsGroupUser.id', ':users')
                )
            );
            $qb->setParameter(':users', $ids);
        }

        if (array_key_exists('instructorGroups', $criteria)) {
            $ids = is_array($criteria['instructorGroups']) ?
                $criteria['instructorGroups'] : [$criteria['instructorGroups']];
            $qb->leftJoin('s.offerings', 'ig_offering');
            $qb->leftJoin('ig_offering.instructorGroups', 'ig_igroup');
            $qb->leftJoin('s.ilmSession', 'ig_ilmSession');
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

            $qb->leftJoin('s.learningMaterials', 'lm_slm');
            $qb->leftJoin('lm_slm.learningMaterial', 'lm_lm');
            $qb->andWhere($qb->expr()->in('lm_lm.id', ':lms'));

            $qb->setParameter(':lms', $ids);
        }
        if (array_key_exists('programs', $criteria)) {
            $ids = is_array($criteria['programs']) ? $criteria['programs'] : [$criteria['programs']];
            $qb->join('s.course', 'p_course');
            $qb->join('p_course.cohorts', 'p_cohort');
            $qb->join('p_cohort.programYear', 'p_programYear');
            $qb->join('p_programYear.program', 'p_program');
            $qb->andWhere($qb->expr()->in('p_program.id', ':programs'));
            $qb->setParameter(':programs', $ids);
        }

        if (array_key_exists('competencies', $criteria)) {
            $ids = is_array($criteria['competencies']) ? $criteria['competencies'] : [$criteria['competencies']];
            $qb->join('s.objectives', 'c_session_objective');
            $qb->join('c_session_objective.parents', 'c_course_objective');
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
            $qb->leftJoin('s.meshDescriptors', 'm_meshDescriptor');
            $qb->leftJoin('s.objectives', 'm_objective');
            $qb->leftJoin('m_objective.meshDescriptors', 'm_objectiveMeshDescriptor');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('m_meshDescriptor.id', ':meshDescriptors'),
                    $qb->expr()->in('m_objectiveMeshDescriptor.id', ':meshDescriptors')
                )
            );
            $qb->setParameter(':meshDescriptors', $ids);
        }

        if (array_key_exists('schools', $criteria)) {
            $ids = is_array($criteria['schools']) ?
                $criteria['schools'] : [$criteria['schools']];
            $qb->join('s.course', 's_course');
            $qb->join('s_course.school', 's_school');
            $qb->andWhere(
                $qb->expr()->in('s_school.id', ':schools')
            );
            $qb->setParameter(':schools', $ids);
        }

        //cleanup all the possible relationship filters
        unset($criteria['schools']);
        unset($criteria['sessions']);
        unset($criteria['terms']);
        unset($criteria['programs']);
        unset($criteria['instructors']);
        unset($criteria['instructorGroups']);
        unset($criteria['learningMaterials']);
        unset($criteria['competencies']);
        unset($criteria['meshDescriptors']);

        if (count($criteria)) {
            foreach ($criteria as $key => $value) {
                $values = is_array($value) ? $value : [$value];
                $qb->andWhere($qb->expr()->in("s.{$key}", ":{$key}"));
                $qb->setParameter(":{$key}", $values);
            }
        }

        if (empty($orderBy)) {
            $orderBy = ['id' => 'ASC'];
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('s.' . $sort, $order);
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
}
