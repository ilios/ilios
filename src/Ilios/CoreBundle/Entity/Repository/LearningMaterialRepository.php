<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Ilios\CoreBundle\Entity\DTO\LearningMaterialDTO;
use Ilios\CoreBundle\Entity\LearningMaterialInterface;

class LearningMaterialRepository extends EntityRepository implements DTORepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT x')->from('IliosCoreBundle:LearningMaterial', 'x');

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
            ->distinct()->from('IliosCoreBundle:LearningMaterial', 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        /** @var LearningMaterialDTO[] $learningMaterialDTOs */
        $learningMaterialDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $learningMaterialDTOs[$arr['id']] = new LearningMaterialDTO(
                $arr['id'],
                $arr['title'],
                $arr['description'],
                $arr['uploadDate'],
                $arr['originalAuthor'],
                $arr['citation'],
                $arr['copyrightPermission'],
                $arr['copyrightRationale'],
                $arr['filename'],
                $arr['mimetype'],
                $arr['filesize'],
                $arr['link'],
                $arr['token']
            );
        }
        $learningMaterialIds = array_keys($learningMaterialDTOs);

        $qb = $this->_em->createQueryBuilder()
            ->select(
                'x.id as xId, userRole.id as userRoleId, owningUser.id as owningUserId, status.id as statusId'
            )
            ->from('IliosCoreBundle:LearningMaterial', 'x')
            ->join('x.userRole', 'userRole')
            ->join('x.owningUser', 'owningUser')
            ->join('x.status', 'status')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', $learningMaterialIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $learningMaterialDTOs[$arr['xId']]->userRole = (int) $arr['userRoleId'];
            $learningMaterialDTOs[$arr['xId']]->owningUser = (int) $arr['owningUserId'];
            $learningMaterialDTOs[$arr['xId']]->status = (int) $arr['statusId'];
        }

        $related = [
            'courseLearningMaterials',
            'sessionLearningMaterials',
        ];
        foreach ($related as $rel) {
            $qb = $this->_em->createQueryBuilder()
                ->select('r.id AS relId, x.id AS learningMaterialId')
                ->from('IliosCoreBundle:LearningMaterial', 'x')
                ->join("x.{$rel}", 'r')
                ->where($qb->expr()->in('x.id', ':ids'))
                ->orderBy('relId')
                ->setParameter('ids', $learningMaterialIds);
            foreach ($qb->getQuery()->getResult() as $arr) {
                $learningMaterialDTOs[$arr['learningMaterialId']]->{$rel}[] = $arr['relId'];
            }
        }
        return array_values($learningMaterialDTOs);
    }
    
    /**
     * Find all the file type learning materials
     * @param integer $limit
     * @param integer $offset
     *
     * @return LearningMaterialInterface[]
     */
    public function findFileLearningMaterials($limit, $offset)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT x')->from('IliosCoreBundle:LearningMaterial', 'x');
        $qb->where($qb->expr()->isNotNull('x.relativePath'));

        $qb->setFirstResult($offset);
        $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    /**
     * Find by a string query
     * @param string $q
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     * @return LearningMaterialInterface[]
     */
    public function findByQ($q, $orderBy, $limit, $offset)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT x')->from('IliosCoreBundle:LearningMaterial', 'x');
        $terms = explode(' ', $q);
        $terms = array_filter($terms, 'strlen');
        if (empty($terms)) {
            return [];
        }

        foreach ($terms as $key => $term) {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('x.title', "?{$key}"),
                $qb->expr()->like('x.description', "?{$key}"),
                $qb->expr()->like('x.originalAuthor', "?{$key}")
            ))
            ->setParameter($key, '%' . $term . '%');
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('x.' . $sort, $order);
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
            'courseLearningMaterials',
            'sessionLearningMaterials',
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

        if (array_key_exists('sessions', $criteria)) {
            $ids = is_array($criteria['sessions']) ? $criteria['sessions'] : [$criteria['sessions']];
            $qb->leftJoin('x.sessionLearningMaterials', 'se_slm');
            $qb->leftJoin('se_slm.session', 'se_session');
            $qb->andWhere($qb->expr()->in('se_session.id', ':sessions'));
            $qb->setParameter(':sessions', $ids);
        }

        if (array_key_exists('courses', $criteria)) {
            $ids = is_array($criteria['courses']) ? $criteria['courses'] : [$criteria['courses']];
            $qb->leftJoin('x.courseLearningMaterials', 'c_clm');
            $qb->leftJoin('c_clm.course', 'c_course');
            $qb->andWhere($qb->expr()->in('c_course.id', ':courses'));
            $qb->setParameter(':courses', $ids);
        }

        if (array_key_exists('instructors', $criteria)) {
            $ids = is_array($criteria['instructors']) ? $criteria['instructors'] : [$criteria['instructors']];
            $qb->leftJoin('x.sessionLearningMaterials', 'i_slm');
            $qb->leftJoin('i_slm.session', 'i_session');
            $qb->leftJoin('i_session.offerings', 'i_offering');
            $qb->leftJoin('i_offering.instructors', 'i_user');
            $qb->leftJoin('i_offering.instructorGroups', 'i_insGroup');
            $qb->leftJoin('i_insGroup.users', 'i_groupUser');
            $qb->leftJoin('i_session.ilmSession', 'i_ilmSession');
            $qb->leftJoin('i_ilmSession.instructors', 'i_ilmInstructor');
            $qb->leftJoin('i_ilmSession.instructorGroups', 'i_ilmInsGroup');
            $qb->leftJoin('i_ilmInsGroup.users', 'i_ilmInsGroupUser');
            $qb->andWhere($qb->expr()->orX(
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
            $qb->leftJoin('x.sessionLearningMaterials', 'ig_slm');
            $qb->leftJoin('ig_slm.session', 'ig_session');
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

        if (array_key_exists('terms', $criteria)) {
            $ids = is_array($criteria['terms']) ? $criteria['terms'] : [$criteria['terms']];
            $qb->leftJoin('x.sessionLearningMaterials', 't_slm');
            $qb->leftJoin('x.courseLearningMaterials', 't_clm');
            $qb->leftJoin('t_slm.session', 't_session');
            $qb->leftJoin('t_session.terms', 't_sessionTerm');
            $qb->leftJoin('t_clm.course', 't_course');
            $qb->leftJoin('t_course.terms', 't_courseTerm');
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->in('t_courseTerm.id', ':terms'),
                $qb->expr()->in('t_sessionTerm.id', ':terms')
            ));
            $qb->setParameter(':terms', $ids);
        }

        if (array_key_exists('meshDescriptors', $criteria)) {
            $ids = is_array($criteria['meshDescriptors']) ?
                $criteria['meshDescriptors'] : [$criteria['meshDescriptors']];
            $qb->leftJoin('x.sessionLearningMaterials', 'm_slm');
            $qb->leftJoin('x.courseLearningMaterials', 'm_clm');
            $qb->leftJoin('m_slm.meshDescriptors', 'm_slmMeshDescriptor');
            $qb->leftJoin('m_clm.meshDescriptors', 'm_clmMeshDescriptor');
            $qb->leftJoin('m_slm.session', 'm_session');
            $qb->leftJoin('m_clm.course', 'm_course');
            $qb->leftJoin('m_session.meshDescriptors', 'm_sessMeshDescriptor');
            $qb->leftJoin('m_course.meshDescriptors', 'm_courseMeshDescriptor');
            $qb->leftJoin('m_session.objectives', 'm_sObjective');
            $qb->leftJoin('m_sObjective.meshDescriptors', 'm_sObjectiveMeshDescriptors');
            $qb->leftJoin('m_course.objectives', 'm_cObjective');
            $qb->leftJoin('m_cObjective.meshDescriptors', 'm_cObjectiveMeshDescriptors');
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->in('m_slmMeshDescriptor.id', ':meshDescriptors'),
                $qb->expr()->in('m_clmMeshDescriptor.id', ':meshDescriptors'),
                $qb->expr()->in('m_sessMeshDescriptor.id', ':meshDescriptors'),
                $qb->expr()->in('m_courseMeshDescriptor.id', ':meshDescriptors'),
                $qb->expr()->in('m_cObjectiveMeshDescriptors.id', ':meshDescriptors'),
                $qb->expr()->in('m_sObjectiveMeshDescriptors.id', ':meshDescriptors')
            ));
            $qb->setParameter(':meshDescriptors', $ids);
        }

        if (array_key_exists('sessionTypes', $criteria)) {
            $ids = is_array($criteria['sessionTypes']) ? $criteria['sessionTypes'] : [$criteria['sessionTypes']];
            $qb->leftJoin('x.sessionLearningMaterials', 'st_slm');
            $qb->leftJoin('st_slm.session', 'st_session');
            $qb->leftJoin('st_session.sessionType', 'st_sessionType');
            $qb->andWhere($qb->expr()->in('st_sessionType.id', ':sessionTypes'));
            $qb->setParameter(':sessionTypes', $ids);
        }

        if (array_key_exists('fullCourses', $criteria)) {
            $ids = is_array($criteria['fullCourses']) ? $criteria['fullCourses'] : [$criteria['fullCourses']];
            $qb->leftJoin('x.sessionLearningMaterials', 'f_slm');
            $qb->leftJoin('f_slm.session', 'f_session');
            $qb->leftJoin('f_session.course', 'f_session_course');
            $qb->leftJoin('x.courseLearningMaterials', 'f_clm');
            $qb->leftJoin('f_clm.course', 'f_course');
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->in('f_course.id', ':courses'),
                $qb->expr()->in('f_session_course.id', ':courses')
            ));
            $qb->setParameter(':courses', $ids);
        }

        //cleanup all the possible relationship filters
        unset($criteria['courses']);
        unset($criteria['sessions']);
        unset($criteria['instructors']);
        unset($criteria['instructorGroups']);
        unset($criteria['terms']);
        unset($criteria['meshDescriptors']);
        unset($criteria['sessionTypes']);
        unset($criteria['fullCourses']);

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
