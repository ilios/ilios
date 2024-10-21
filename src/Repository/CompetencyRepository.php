<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Competency;
use App\Service\DefaultDataImporter;
use App\Service\DTOCacheManager;
use App\Traits\ImportableEntityRepository;
use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\DTO\CompetencyDTO;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

use function array_values;
use function array_keys;

class CompetencyRepository extends ServiceEntityRepository implements
    DTORepositoryInterface,
    RepositoryInterface,
    DataImportRepositoryInterface
{
    use ManagerRepository;
    use ImportableEntityRepository;

    public function __construct(
        ManagerRegistry $registry,
        protected DTOCacheManager $cacheManager,
    ) {
        parent::__construct($registry, Competency::class);
    }

    public function hydrateDTOsFromIds(array $ids): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder()->select('x')->distinct()->from(Competency::class, 'x');
        $qb->where($qb->expr()->in('x.id', ':ids'));
        $qb->setParameter(':ids', $ids);
        $dtos = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $dtos[$arr['id']] = new CompetencyDTO(
                $arr['id'],
                $arr['title'],
                $arr['active']
            );
        }
        $competencyIds = array_keys($dtos);
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('x.id as competencyId, s.id as schoolId, p.id as parentId')
            ->from(Competency::class, 'x')
            ->join('x.school', 's')
            ->leftJoin('x.parent', 'p')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', $competencyIds);
        foreach ($qb->getQuery()->getResult() as $arr) {
            $dtos[$arr['competencyId']]->school = (int) $arr['schoolId'];
            $dtos[$arr['competencyId']]->parent = $arr['parentId'] ? (int)$arr['parentId'] : null;
        }

        $dtos = $this->attachRelatedToDtos(
            $dtos,
            [
                'programYearObjectives',
                'children',
                'aamcPcrses',
                'programYears',
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
            $qb->leftJoin('x.children', 'se_subcompetency');
            $qb->leftJoin('x.programYearObjectives', 'se_py_objective');
            $qb->leftJoin('se_py_objective.courseObjectives', 'se_course_objective');
            $qb->leftJoin('se_course_objective.sessionObjectives', 'se_session_objective');
            $qb->leftJoin('se_session_objective.session', 'se_session');
            $qb->leftJoin('se_subcompetency.programYearObjectives', 'se_py_objective2');
            $qb->leftJoin('se_py_objective2.courseObjectives', 'se_course_objective2');
            $qb->leftJoin('se_course_objective2.sessionObjectives', 'se_session_objective2');
            $qb->leftJoin('se_session_objective2.session', 'se_session2');
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
            $qb->leftJoin('x.children', 'st_subcompetency');
            $qb->leftJoin('x.programYearObjectives', 'st_py_objective');
            $qb->leftJoin('st_py_objective.courseObjectives', 'st_course_objective');
            $qb->leftJoin('st_course_objective.sessionObjectives', 'st_session_objective');
            $qb->leftJoin('st_session_objective.session', 'st_session');
            $qb->leftJoin('st_session.sessionType', 'st_sessionType');
            $qb->leftJoin('st_subcompetency.programYearObjectives', 'st_py_objective2');
            $qb->leftJoin('st_py_objective2.courseObjectives', 'st_course_objective2');
            $qb->leftJoin('st_course_objective2.sessionObjectives', 'st_session_objective2');
            $qb->leftJoin('st_session_objective2.session', 'st_session2');
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
            $qb->leftJoin('x.children', 'c_subcompetency');
            $qb->leftJoin('x.programYearObjectives', 'c_py_objective');
            $qb->leftJoin('c_py_objective.courseObjectives', 'c_course_objective');
            $qb->leftJoin('c_course_objective.course', 'c_course');
            $qb->leftJoin('c_subcompetency.programYearObjectives', 'c_py_objective2');
            $qb->leftJoin('c_py_objective2.courseObjectives', 'c_course_objective2');
            $qb->leftJoin('c_course_objective2.course', 'c_course2');
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
            $qb->leftJoin('x.children', 't_subcompetency');
            $qb->leftJoin('x.programYearObjectives', 't_py_objective');
            $qb->leftJoin('t_py_objective.courseObjectives', 't_course_objective');
            $qb->leftJoin('t_course_objective.course', 't_course');
            $qb->leftJoin('t_course.terms', 't_term');
            $qb->leftJoin('t_course_objective.sessionObjectives', 't_session_objective');
            $qb->leftJoin('t_session_objective.session', 't_session');
            $qb->leftJoin('t_session.terms', 't_term2');
            $qb->leftJoin('t_subcompetency.programYearObjectives', 't_py_objective2');
            $qb->leftJoin('t_py_objective2.courseObjectives', 't_course_objective2');
            $qb->leftJoin('t_course_objective2.course', 't_course2');
            $qb->leftJoin('t_course2.terms', 't_term3');
            $qb->leftJoin('t_course_objective2.sessionObjectives', 't_session_objective2');
            $qb->leftJoin('t_session_objective2.session', 't_session2');
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
            $qb->join('x.school', 'sc_school');
            $qb->andWhere($qb->expr()->in('sc_school.id', ':schools'));
            $qb->setParameter(':schools', $ids);
        }

        if (array_key_exists('academicYears', $criteria)) {
            $ids = is_array($criteria['academicYears']) ? $criteria['academicYears'] : [$criteria['academicYears']];
            $qb->leftJoin('x.children', 'y_subcompetency');
            $qb->leftJoin('x.programYearObjectives', 'y_py_objective');
            $qb->leftJoin('y_py_objective.courseObjectives', 'y_course_objective');
            $qb->leftJoin('y_course_objective.course', 'y_course');
            $qb->leftJoin('y_subcompetency.programYearObjectives', 'y_py_objective2');
            $qb->leftJoin('y_py_objective2.courseObjectives', 'y_course_objective2');
            $qb->leftJoin('y_course_objective2.course', 'y_course2');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('y_course.year', ':academicYears'),
                    $qb->expr()->in('y_course2.year', ':academicYears')
                )
            );
            $qb->setParameter(':academicYears', $ids);
        }

        //cleanup all the possible relationship filters
        unset($criteria['schools']);
        unset($criteria['courses']);
        unset($criteria['sessions']);
        unset($criteria['sessionTypes']);
        unset($criteria['terms']);
        unset($criteria['academicYears']);

        $this->attachClosingCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
    }

    public function import(array $data, string $type, array $referenceMap): array
    {
        return match ($type) {
            DefaultDataImporter::COMPETENCY => $this->importCompetencies($data, $type, $referenceMap),
            DefaultDataImporter::COMPETENCY_X_AAMC_PCRS => $this->importCompetencyToPcrsMapping($data, $referenceMap),
            default => throw new Exception("Unable to import data of type $type."),
        };
    }

    protected function importCompetencies(array $data, string $type, array $referenceMap): array
    {
        // `competency_id`,`title`,`parent_competency_id`,`school_id`, `active`
        $entity = new Competency();
        $entity->setId($data[0]);
        $entity->setTitle($data[1]);
        if (! empty($data[2])) {
            $entity->setParent($referenceMap[$type . $data[2]]);
        }
        $entity->setSchool($referenceMap[DefaultDataImporter::SCHOOL . $data[3]]);
        $entity->setActive($data[4]);
        $this->importEntity($entity);
        $referenceMap[$type . $entity->getId()] = $entity;
        return $referenceMap;
    }

    protected function importCompetencyToPcrsMapping(array $data, array $referenceMap): array
    {
        // `competency_id`,`pcrs_id`
        /** @var Competency $entity */
        $entity = $referenceMap[DefaultDataImporter::COMPETENCY . $data[0]];
        $entity->addAamcPcrs($referenceMap[DefaultDataImporter::AAMC_PCRS . $data[1]]);
        $this->update($entity, true, true);
        return $referenceMap;
    }
}
