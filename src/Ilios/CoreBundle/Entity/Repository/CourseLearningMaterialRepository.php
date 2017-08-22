<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Ilios\CoreBundle\Entity\DTO\CourseLearningMaterialDTO;

/**
 * Class CourseLearningMaterialRepository
 */
class CourseLearningMaterialRepository extends EntityRepository implements DTORepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT x')->from('IliosCoreBundle:CourseLearningMaterial', 'x');

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
            ->distinct()->from('IliosCoreBundle:CourseLearningMaterial', 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
        /** @var CourseLearningMaterialDTO[] $courseLearningMaterialDTOs */
        $courseLearningMaterialDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $courseLearningMaterialDTOs[$arr['id']] = new CourseLearningMaterialDTO(
                $arr['id'],
                $arr['notes'],
                $arr['required'],
                $arr['publicNotes'],
                $arr['position'],
                $arr['startDate'],
                $arr['endDate']
            );
        }
        $courseLearningMaterialIds = array_keys($courseLearningMaterialDTOs);

        $qb = $this->_em->createQueryBuilder()
            ->select(
                'x.id as xId, learningMaterial.id AS learningMaterialId, ' .
                'course.id AS courseId, course.locked AS courseIsLocked, course.archived AS courseIsArchived, ' .
                'status.id as statusId, school.id AS schoolId'
            )
            ->from('IliosCoreBundle:CourseLearningMaterial', 'x')
            ->join('x.course', 'course')
            ->join('course.school', 'school')
            ->join('x.learningMaterial', 'learningMaterial')
            ->leftJoin('learningMaterial.status', 'status')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', $courseLearningMaterialIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $courseLearningMaterialDTOs[$arr['xId']]->course = (int) $arr['courseId'];
            $courseLearningMaterialDTOs[$arr['xId']]->courseIsLocked = (bool) $arr['courseIsLocked'];
            $courseLearningMaterialDTOs[$arr['xId']]->courseIsArchived = (bool) $arr['courseIsArchived'];
            $courseLearningMaterialDTOs[$arr['xId']]->school = (int) $arr['schoolId'];
            $courseLearningMaterialDTOs[$arr['xId']]->learningMaterial = (int) $arr['learningMaterialId'];
            $courseLearningMaterialDTOs[$arr['xId']]->status = (int) $arr['statusId'];
        }

        $related = [
            'meshDescriptors'
        ];
        foreach ($related as $rel) {
            $qb = $this->_em->createQueryBuilder()
                ->select('r.id AS relId, x.id AS courseLearningMaterialId')
                ->from('IliosCoreBundle:CourseLearningMaterial', 'x')
                ->join("x.{$rel}", 'r')
                ->where($qb->expr()->in('x.id', ':ids'))
                ->orderBy('relId')
                ->setParameter('ids', $courseLearningMaterialIds);
            foreach ($qb->getQuery()->getResult() as $arr) {
                $courseLearningMaterialDTOs[$arr['courseLearningMaterialId']]->{$rel}[] = $arr['relId'];
            }
        }
        return array_values($courseLearningMaterialDTOs);
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
        if (array_key_exists('meshDescriptors', $criteria)) {
            if (is_array($criteria['meshDescriptors'])) {
                $ids = $criteria['meshDescriptors'];
            } else {
                $ids = [$criteria['meshDescriptors']];
            }
            $qb->join('x.meshDescriptors', 'st');
            $qb->andWhere($qb->expr()->in('st.id', ':meshDescriptors'));
            $qb->setParameter(':meshDescriptors', $ids);
        }

        //cleanup all the possible relationship filters
        unset($criteria['meshDescriptors']);

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
