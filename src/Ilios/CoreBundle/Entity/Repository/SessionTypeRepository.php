<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Ilios\CoreBundle\Entity\DTO\SessionTypeDTO;

class SessionTypeRepository extends EntityRepository implements DTORepositoryInterface
{

    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT st')->from('IliosCoreBundle:SessionType', 'st');

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
        $qb = $this->_em->createQueryBuilder()
            ->select('st')
            ->distinct()
            ->from('IliosCoreBundle:SessionType', 'st');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        $sessionTypeDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $sessionTypeDTOs[$arr['id']] = new SessionTypeDTO(
                $arr['id'],
                $arr['title'],
                $arr['calendarColor'],
                $arr['assessment'],
                $arr['active']
            );
        }
        $sessionTypeIds = array_keys($sessionTypeDTOs);

        $qb = $this->_em->createQueryBuilder()
            ->select('st.id as sessionTypeId, s.id as schoolId, a.id as assessmentOptionId')
            ->from('IliosCoreBundle:SessionType', 'st')
            ->join('st.school', 's')
            ->leftJoin('st.assessmentOption', 'a')
            ->where($qb->expr()->in('st.id', ':ids'))
            ->setParameter('ids', $sessionTypeIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $sessionTypeDTOs[$arr['sessionTypeId']]->school = (int) $arr['schoolId'];
            $sessionTypeDTOs[$arr['sessionTypeId']]->assessmentOption =
                $arr['assessmentOptionId']?(int)$arr['assessmentOptionId']:null;
        }

        $related = [
            'aamcMethods',
            'sessions'
        ];

        foreach ($related as $rel) {
            $qb = $this->_em->createQueryBuilder()
                ->select('r.id AS relId, x.id AS sessionTypeId')->from('IliosCoreBundle:SessionType', 'x')
                ->join("x.{$rel}", 'r')
                ->where($qb->expr()->in('x.id', ':sessionTypeIds'))
                ->orderBy('relId')
                ->setParameter('sessionTypeIds', $sessionTypeIds);

            foreach ($qb->getQuery()->getResult() as $arr) {
                $sessionTypeDTOs[$arr['sessionTypeId']]->{$rel}[] = $arr['relId'];
            }
        }

        return array_values($sessionTypeDTOs);
    }

    /**
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
            $qb->join('st.sessions', 'se_session');
            $qb->andWhere($qb->expr()->in('se_session.id', ':sessions'));
            $qb->setParameter(':sessions', $ids);
        }

        if (array_key_exists('courses', $criteria)) {
            $ids = is_array($criteria['courses']) ? $criteria['courses'] : [$criteria['courses']];
            $qb->join('st.sessions', 'co_session');
            $qb->join('co_session.course', 'co_course');
            $qb->andWhere($qb->expr()->in('co_course.id', ':courses'));
            $qb->setParameter(':courses', $ids);
        }

        if (array_key_exists('instructors', $criteria)) {
            $ids = is_array($criteria['instructors']) ? $criteria['instructors'] : [$criteria['instructors']];
            $qb->leftJoin('st.sessions', 'i_session');
            $qb->leftJoin('i_session.offerings', 'i_offering');
            $qb->leftJoin('i_offering.instructors', 'i_instructor');
            $qb->leftJoin('i_offering.instructorGroups', 'i_insGroup');
            $qb->leftJoin('i_insGroup.users', 'i_insGroupUser');
            $qb->leftJoin('i_session.ilmSession', 'i_ilmSession');
            $qb->leftJoin('i_ilmSession.instructors', 'i_ilmInstructor');
            $qb->leftJoin('i_ilmSession.instructorGroups', 'i_ilmInsGroup');
            $qb->leftJoin('i_ilmInsGroup.users', 'i_ilmInsGroupUser');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('i_instructor.id', ':users'),
                    $qb->expr()->in('i_insGroupUser.id', ':users'),
                    $qb->expr()->in('i_ilmInstructor.id', ':users'),
                    $qb->expr()->in('i_ilmInsGroupUser.id', ':users')
                )
            );
            $qb->setParameter(':users', $ids);
        }

        if (array_key_exists('instructorGroups', $criteria)) {
            $ids = is_array($criteria['instructorGroups'])
                ? $criteria['instructorGroups'] : [$criteria['instructorGroups']];
            $qb->leftJoin('st.sessions', 'ig_session');
            $qb->leftJoin('ig_session.offerings', 'ig_offering');
            $qb->leftJoin('ig_offering.instructorGroups', 'ig_insGroup');
            $qb->leftJoin('ig_session.ilmSession', 'ig_ilmSession');
            $qb->leftJoin('ig_ilmSession.instructorGroups', 'ig_ilmInsGroup');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('ig_insGroup.id', ':igroups'),
                    $qb->expr()->in('ig_ilmInsGroup.id', ':igroups')
                )
            );
            $qb->setParameter(':igroups', $ids);
        }

        if (array_key_exists('competencies', $criteria)) {
            $ids = is_array($criteria['competencies']) ? $criteria['competencies'] : [$criteria['competencies']];
            $qb->join('st.sessions', 'c_session');
            $qb->join('c_session.objectives', 'c_session_objective');
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
            $qb->leftJoin('st.sessions', 'm_session');
            $qb->leftJoin('m_session.meshDescriptors', 'm_meshDescriptor');
            $qb->leftJoin('m_session.objectives', 'm_objective');
            $qb->leftJoin('m_objective.meshDescriptors', 'm_objectiveMeshDescriptor');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('m_meshDescriptor.id', ':meshDescriptors'),
                    $qb->expr()->in('m_objectiveMeshDescriptor.id', ':meshDescriptors')
                )
            );
            $qb->setParameter(':meshDescriptors', $ids);
        }

        if (array_key_exists('learningMaterials', $criteria)) {
            $ids = is_array($criteria['learningMaterials']) ?
                $criteria['learningMaterials'] : [$criteria['learningMaterials']];

            $qb->join('st.sessions', 'lm_session');
            $qb->join('lm_session.course', 'lm_course');
            $qb->leftJoin('lm_session.learningMaterials', 'lm_slm');
            $qb->leftJoin('lm_slm.learningMaterial', 'lm_lm1');
            $qb->leftJoin('lm_course.learningMaterials', 'lm_clm');
            $qb->leftJoin('lm_clm.learningMaterial', 'lm_lm2');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('lm_lm1.id', ':lms'),
                    $qb->expr()->in('lm_lm2.id', ':lms')
                )
            );
            $qb->setParameter(':lms', $ids);
        }

        if (array_key_exists('programs', $criteria)) {
            $ids = is_array($criteria['programs']) ? $criteria['programs'] : [$criteria['programs']];
            $qb->join('st.sessions', 'p_session');
            $qb->join('p_session.course', 'p_course');
            $qb->join('p_course.cohorts', 'p_cohort');
            $qb->join('p_cohort.programYear', 'p_programYear');
            $qb->join('p_programYear.program', 'p_program');
            $qb->andWhere($qb->expr()->in('p_program.id', ':programs'));
            $qb->setParameter(':programs', $ids);
        }

        if (array_key_exists('schools', $criteria)) {
            $ids = is_array($criteria['schools']) ? $criteria['schools'] : [$criteria['schools']];
            $qb->join('st.school', 'sc_school');
            $qb->andWhere($qb->expr()->in('sc_school.id', ':schools'));
            $qb->setParameter(':schools', $ids);
        }

        if (array_key_exists('terms', $criteria)) {
            $ids = is_array($criteria['terms']) ? $criteria['terms'] : [$criteria['terms']];
            $qb->join('st.sessions', 't_session');
            $qb->leftJoin('t_session.terms', 't_session_term');
            $qb->leftJoin('t_session.course', 't_course');
            $qb->leftJoin('t_course.terms', 't_course_term');

            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('t_session_term.id', ':terms'),
                    $qb->expr()->in('t_course_term.id', ':terms')
                )
            );
            $qb->setParameter(':terms', $ids);
        }

        unset($criteria['schools']);
        unset($criteria['programs']);
        unset($criteria['sessions']);
        unset($criteria['courses']);
        unset($criteria['instructors']);
        unset($criteria['instructorGroups']);
        unset($criteria['competencies']);
        unset($criteria['meshDescriptors']);
        unset($criteria['learningMaterials']);
        unset($criteria['terms']);

        if (count($criteria)) {
            foreach ($criteria as $key => $value) {
                $values = is_array($value) ? $value : [$value];
                $qb->andWhere($qb->expr()->in("st.{$key}", ":{$key}"));
                $qb->setParameter(":{$key}", $values);
            }
        }

        if (empty($orderBy)) {
            $orderBy = ['id' => 'ASC'];
        }
        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('st.'.$sort, $order);
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
