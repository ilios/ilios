<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Ilios\CoreBundle\Entity\DTO\InstructorGroupDTO;

/**
 * Class InstructorGroupRepository
 */
class InstructorGroupRepository extends EntityRepository implements DTORepositoryInterface
{

    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT x')->from('IliosCoreBundle:InstructorGroup', 'x');

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
        $qb = $this->_em->createQueryBuilder()->select('x')
            ->distinct()->from('IliosCoreBundle:InstructorGroup', 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        /** @var InstructorGroupDTO[] $instructorGroupDTOs */
        $instructorGroupDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $instructorGroupDTOs[$arr['id']] = new InstructorGroupDTO(
                $arr['id'],
                $arr['title']
            );
        }
        $instructorGroupIds = array_keys($instructorGroupDTOs);

        $qb = $this->_em->createQueryBuilder()
            ->select(
                'x.id as xId, school.id AS schoolId'
            )
            ->from('IliosCoreBundle:InstructorGroup', 'x')
            ->join('x.school', 'school')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', $instructorGroupIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $instructorGroupDTOs[$arr['xId']]->school = (int) $arr['schoolId'];
        }

        $related = [
            'learnerGroups',
            'ilmSessions',
            'users',
            'offerings',
        ];
        foreach ($related as $rel) {
            $qb = $this->_em->createQueryBuilder()
                ->select('r.id AS relId, x.id AS instructorGroupId')
                ->from('IliosCoreBundle:InstructorGroup', 'x')
                ->join("x.{$rel}", 'r')
                ->where($qb->expr()->in('x.id', ':ids'))
                ->orderBy('relId')
                ->setParameter('ids', $instructorGroupIds);
            foreach ($qb->getQuery()->getResult() as $arr) {
                $instructorGroupDTOs[$arr['instructorGroupId']]->{$rel}[] = $arr['relId'];
            }
        }
        return array_values($instructorGroupDTOs);
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
        $related = [
            'learnerGroups',
            'ilmSessions',
            'users',
            'offerings',
        ];
        foreach ($related as $rel) {
            if (array_key_exists($rel, $criteria)) {
                $ids = is_array($criteria[$rel]) ?
                    $criteria[$rel] : [$criteria[$rel]];
                $alias = "alias_${rel}";
                $param = ":${rel}";
                $qb->join("x.${rel}", $alias);
                $qb->andWhere($qb->expr()->in("${alias}.id", $param));
                $qb->setParameter($param, $ids);
            }
            unset($criteria[$rel]);
        }

        if (array_key_exists('courses', $criteria)) {
            $ids = is_array($criteria['courses']) ? $criteria['courses'] : [$criteria['courses']];
            $qb->leftJoin('x.ilmSessions', 'c_ilm');
            $qb->leftJoin('x.offerings', 'c_offering');
            $qb->leftJoin('c_ilm.session', 'c_session');
            $qb->leftJoin('c_offering.session', 'c_session2');
            $qb->leftJoin('c_session.course', 'c_course');
            $qb->leftJoin('c_session2.course', 'c_course2');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('c_course.id', ':courses'),
                    $qb->expr()->in('c_course2.id', ':courses')
                )
            );
            $qb->setParameter(':courses', $ids);
        }

        if (array_key_exists('sessions', $criteria)) {
            $ids = is_array($criteria['sessions']) ? $criteria['sessions'] : [$criteria['sessions']];
            $qb->leftJoin('x.ilmSessions', 'se_ilm');
            $qb->leftJoin('x.offerings', 'se_offering');
            $qb->leftJoin('se_ilm.session', 'se_session');
            $qb->leftJoin('se_offering.session', 'se_session2');
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
            $qb->leftJoin('x.ilmSessions', 'st_ilm');
            $qb->leftJoin('x.offerings', 'st_offering');
            $qb->leftJoin('st_ilm.session', 'st_session');
            $qb->leftJoin('st_offering.session', 'st_session2');
            $qb->leftJoin('st_session.sessionType', 'st_sessionType');
            $qb->leftJoin('st_session2.sessionType', 'st_sessionType2');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('st_sessionType.id', ':sessionTypes'),
                    $qb->expr()->in('st_sessionType2.id', ':sessionTypes')
                )
            );
            $qb->setParameter(':sessionTypes', $ids);
        }

        if (array_key_exists('instructors', $criteria)) {
            $ids = is_array($criteria['instructors']) ? $criteria['instructors'] : [$criteria['instructors']];
            $qb->join('x.users', 'i_instructor');
            $qb->andWhere($qb->expr()->in('i_instructor.id', ':instructors'));
            $qb->setParameter(':instructors', $ids);
        }

        if (array_key_exists('learningMaterials', $criteria)) {
            $ids = is_array($criteria['learningMaterials'])
                ? $criteria['learningMaterials'] : [$criteria['learningMaterials']];
            $qb->leftJoin('x.ilmSessions', 'lm_ilm');
            $qb->leftJoin('x.offerings', 'lm_offering');
            $qb->leftJoin('lm_ilm.session', 'lm_session');
            $qb->leftJoin('lm_offering.session', 'lm_session2');
            $qb->leftJoin('lm_session.learningMaterials', 'lm_slm');
            $qb->leftJoin('lm_session2.learningMaterials', 'lm_slm2');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('lm_slm.id', ':learningMaterials'),
                    $qb->expr()->in('lm_slm2.id', ':learningMaterials')
                )
            );
            $qb->setParameter(':learningMaterials', $ids);
        }

        if (array_key_exists('terms', $criteria)) {
            $ids = is_array($criteria['terms'])
                ? $criteria['terms'] : [$criteria['terms']];
            $qb->leftJoin('x.ilmSessions', 't_ilm');
            $qb->leftJoin('x.offerings', 't_offering');
            $qb->leftJoin('t_ilm.session', 't_session');
            $qb->leftJoin('t_offering.session', 't_session2');
            $qb->leftJoin('t_session.terms', 't_term');
            $qb->leftJoin('t_session2.terms', 't_term2');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('t_term.id', ':terms'),
                    $qb->expr()->in('t_term2.id', ':terms')
                )
            );
            $qb->setParameter(':terms', $ids);
        }

        if (array_key_exists('schools', $criteria)) {
            $ids = is_array($criteria['schools']) ? $criteria['schools'] : [$criteria['schools']];
            $qb->join('x.school', 'sc_school');
            $qb->andWhere($qb->expr()->in('sc_school.id', ':schools'));
            $qb->setParameter(':schools', $ids);
        }

        unset($criteria['schools']);
        unset($criteria['courses']);
        unset($criteria['sessions']);
        unset($criteria['sessionTypes']);
        unset($criteria['instructors']);
        unset($criteria['learningMaterials']);
        unset($criteria['terms']);
        

        if (count($criteria)) {
            foreach ($criteria as $key => $value) {
                $values = is_array($value) ? $value : [$value];
                $qb->andWhere($qb->expr()->in("x.{$key}", ":{$key}"));
                $qb->setParameter(":{$key}", $values);
            }
        }

        if (empty($orderBy)) {
            $orderBy = ['id' => 'ASC'];
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('x.'.$sort, $order);
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
