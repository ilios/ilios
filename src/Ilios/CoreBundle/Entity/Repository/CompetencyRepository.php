<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Ilios\CoreBundle\Entity\DTO\CompetencyDTO;

/**
 * Class CompetencyRepository
 */
class CompetencyRepository extends EntityRepository implements DTORepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT c')->from('IliosCoreBundle:Competency', 'c');

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
        $qb = $this->_em->createQueryBuilder()->select('c')->distinct()->from('IliosCoreBundle:Competency', 'c');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
        $competencyDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $competencyDTOs[$arr['id']] = new CompetencyDTO(
                $arr['id'],
                $arr['title'],
                $arr['active']
            );
        }
        $competencyIds = array_keys($competencyDTOs);
        $qb = $this->_em->createQueryBuilder()
            ->select('c.id as competencyId, s.id as schoolId, p.id as parentId')
            ->from('IliosCoreBundle:Competency', 'c')
            ->join('c.school', 's')
            ->leftJoin('c.parent', 'p')
            ->where($qb->expr()->in('c.id', ':ids'))
            ->setParameter('ids', $competencyIds);
        foreach ($qb->getQuery()->getResult() as $arr) {
            $competencyDTOs[$arr['competencyId']]->school = (int) $arr['schoolId'];
            $competencyDTOs[$arr['competencyId']]->parent = $arr['parentId']?(int)$arr['parentId']:null;
        }
        $related = [
            'objectives',
            'children',
            'aamcPcrses',
            'programYears'
        ];
        foreach ($related as $rel) {
            $qb = $this->_em->createQueryBuilder()
                ->select('r.id AS relId, c.id AS competencyId')->from('IliosCoreBundle:Competency', 'c')
                ->join("c.{$rel}", 'r')
                ->where($qb->expr()->in('c.id', ':competencyIds'))
                ->orderBy('relId')
                ->setParameter('competencyIds', $competencyIds);
            foreach ($qb->getQuery()->getResult() as $arr) {
                $competencyDTOs[$arr['competencyId']]->{$rel}[] = $arr['relId'];
            }
        }
        return array_values($competencyDTOs);
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
            $qb->leftJoin('c.children', 'se_subcompetency');
            $qb->leftJoin('c.objectives', 'se_py_objective');
            $qb->leftJoin('se_py_objective.children', 'se_course_objective');
            $qb->leftJoin('se_course_objective.children', 'se_session_objective');
            $qb->leftJoin('se_session_objective.sessions', 'se_session');
            $qb->leftJoin('se_subcompetency.objectives', 'se_py_objective2');
            $qb->leftJoin('se_py_objective2.children', 'se_course_objective2');
            $qb->leftJoin('se_course_objective2.children', 'se_session_objective2');
            $qb->leftJoin('se_session_objective2.sessions', 'se_session2');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('se_session.id', ':sessions'),
                    $qb->expr()->in('se_session2.id', ':sessions')
                )
            );
            $qb->setParameter(':sessions', $ids);
        }

        if (array_key_exists('sessionTypes', $criteria)) {
            $ids = is_array($criteria['sessionTypes']) ? $criteria['sessionTypes'] : [$criteria['sessionTypes']];
            $qb->leftJoin('c.children', 'st_subcompetency');
            $qb->leftJoin('c.objectives', 'st_py_objective');
            $qb->leftJoin('st_py_objective.children', 'st_course_objective');
            $qb->leftJoin('st_course_objective.children', 'st_session_objective');
            $qb->leftJoin('st_session_objective.sessions', 'st_session');
            $qb->leftJoin('st_session.sessionType', 'st_sessionType');
            $qb->leftJoin('st_subcompetency.objectives', 'st_py_objective2');
            $qb->leftJoin('st_py_objective2.children', 'st_course_objective2');
            $qb->leftJoin('st_course_objective2.children', 'st_session_objective2');
            $qb->leftJoin('st_session_objective2.sessions', 'st_session2');
            $qb->leftJoin('st_session2.sessionType', 'st_sessionType2');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('st_sessionType.id', ':sessionTypes'),
                    $qb->expr()->in('st_sessionType2.id', ':sessionTypes')
                )
            );
            $qb->setParameter(':sessionTypes', $ids);
        }

        if (array_key_exists('courses', $criteria)) {
            $ids = is_array($criteria['courses']) ? $criteria['courses'] : [$criteria['courses']];
            $qb->leftJoin('c.children', 'c_subcompetency');
            $qb->leftJoin('c.objectives', 'c_py_objective');
            $qb->leftJoin('c_py_objective.children', 'c_course_objective');
            $qb->leftJoin('c_course_objective.courses', 'c_course');
            $qb->leftJoin('c_subcompetency.objectives', 'c_py_objective2');
            $qb->leftJoin('c_py_objective2.children', 'c_course_objective2');
            $qb->leftJoin('c_course_objective2.courses', 'c_course2');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('c_course.id', ':courses'),
                    $qb->expr()->in('c_course2.id', ':courses')
                )
            );
            $qb->setParameter(':courses', $ids);
        }

        if (array_key_exists('terms', $criteria)) {
            $ids = is_array($criteria['terms']) ? $criteria['terms'] : [$criteria['terms']];
            $qb->leftJoin('c.children', 't_subcompetency');
            $qb->leftJoin('c.objectives', 't_py_objective');
            $qb->leftJoin('t_py_objective.children', 't_course_objective');
            $qb->leftJoin('t_course_objective.courses', 't_course');
            $qb->leftJoin('t_course.terms', 't_term');
            $qb->leftJoin('t_course_objective.children', 't_session_objective');
            $qb->leftJoin('t_session_objective.sessions', 't_session');
            $qb->leftJoin('t_session.terms', 't_term2');
            $qb->leftJoin('t_subcompetency.objectives', 't_py_objective2');
            $qb->leftJoin('t_py_objective2.children', 't_course_objective2');
            $qb->leftJoin('t_course_objective2.courses', 't_course2');
            $qb->leftJoin('t_course2.terms', 't_term3');
            $qb->leftJoin('t_course_objective2.children', 't_session_objective2');
            $qb->leftJoin('t_session_objective2.sessions', 't_session2');
            $qb->leftJoin('t_session2.terms', 't_term4');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('t_term.id', ':terms'),
                    $qb->expr()->in('t_term2.id', ':terms'),
                    $qb->expr()->in('t_term3.id', ':terms'),
                    $qb->expr()->in('t_term4.id', ':terms')
                )
            );
            $qb->setParameter(':terms', $ids);
        }

        if (array_key_exists('schools', $criteria)) {
            $ids = is_array($criteria['schools']) ? $criteria['schools'] : [$criteria['schools']];
            $qb->join('c.school', 'sc_school');
            $qb->andWhere($qb->expr()->in('sc_school.id', ':schools'));
            $qb->setParameter(':schools', $ids);
        }

        //cleanup all the possible relationship filters
        unset($criteria['schools']);
        unset($criteria['courses']);
        unset($criteria['sessions']);
        unset($criteria['sessionTypes']);
        unset($criteria['terms']);

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
                $qb->addOrderBy('c.'.$sort, $order);
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
