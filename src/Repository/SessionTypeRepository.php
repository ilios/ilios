<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\SessionType;
use App\Service\DefaultDataImporter;
use App\Service\DTOCacheManager;
use App\Traits\ImportableEntityRepository;
use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\DTO\SessionTypeDTO;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

use function array_values;
use function array_keys;

class SessionTypeRepository extends ServiceEntityRepository implements
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
        parent::__construct($registry, SessionType::class);
    }

    public function hydrateDTOsFromIds(array $ids): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('x')
            ->distinct()
            ->from(SessionType::class, 'x');
        $qb->where($qb->expr()->in('x.id', ':ids'));
        $qb->setParameter(':ids', $ids);

        $dtos = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $dtos[$arr['id']] = new SessionTypeDTO(
                $arr['id'],
                $arr['title'],
                $arr['calendarColor'],
                $arr['assessment'],
                $arr['active']
            );
        }
        $sessionTypeIds = array_keys($dtos);

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('st.id as sessionTypeId, s.id as schoolId, a.id as assessmentOptionId')
            ->from(SessionType::class, 'st')
            ->join('st.school', 's')
            ->leftJoin('st.assessmentOption', 'a')
            ->where($qb->expr()->in('st.id', ':ids'))
            ->setParameter('ids', $sessionTypeIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $dtos[$arr['sessionTypeId']]->school = (int) $arr['schoolId'];
            $dtos[$arr['sessionTypeId']]->assessmentOption =
                $arr['assessmentOptionId'] ? (int)$arr['assessmentOptionId'] : null;
        }

        $dtos = $this->attachRelatedToDtos(
            $dtos,
            [
                'aamcMethods',
                'sessions',
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

        if (array_key_exists('courses', $criteria)) {
            $ids = is_array($criteria['courses']) ? $criteria['courses'] : [$criteria['courses']];
            $qb->join('x.sessions', 'co_session');
            $qb->join('co_session.course', 'co_course');
            $qb->andWhere($qb->expr()->in('co_course.id', ':courses'));
            $qb->setParameter(':courses', $ids);
        }

        if (array_key_exists('instructors', $criteria)) {
            $ids = is_array($criteria['instructors']) ? $criteria['instructors'] : [$criteria['instructors']];
            $qb->leftJoin('x.sessions', 'i_session');
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
            $qb->leftJoin('x.sessions', 'ig_session');
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
            $qb->join('x.sessions', 'c_session');
            $qb->join('c_session.sessionObjectives', 'c_session_x_objective');
            $qb->join('c_session_x_objective.courseObjectives', 'c_course_objective');
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
            $qb->leftJoin('x.sessions', 'm_session');
            $qb->leftJoin('m_session.meshDescriptors', 'm_meshDescriptor');
            $qb->leftJoin('m_session.sessionObjectives', 'm_session_x_objective');
            $qb->leftJoin('m_session_x_objective.meshDescriptors', 'm_objectiveMeshDescriptor');
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

            $qb->join('x.sessions', 'lm_session');
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
            $qb->join('x.sessions', 'p_session');
            $qb->join('p_session.course', 'p_course');
            $qb->join('p_course.cohorts', 'p_cohort');
            $qb->join('p_cohort.programYear', 'p_programYear');
            $qb->join('p_programYear.program', 'p_program');
            $qb->andWhere($qb->expr()->in('p_program.id', ':programs'));
            $qb->setParameter(':programs', $ids);
        }

        if (array_key_exists('schools', $criteria)) {
            $ids = is_array($criteria['schools']) ? $criteria['schools'] : [$criteria['schools']];
            $qb->join('x.school', 'sc_school');
            $qb->andWhere($qb->expr()->in('sc_school.id', ':schools'));
            $qb->setParameter(':schools', $ids);
        }

        if (array_key_exists('terms', $criteria)) {
            $ids = is_array($criteria['terms']) ? $criteria['terms'] : [$criteria['terms']];
            $qb->join('x.sessions', 't_session');
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

        if (array_key_exists('academicYears', $criteria)) {
            $ids = is_array($criteria['academicYears']) ? $criteria['academicYears'] : [$criteria['academicYears']];
            $qb->join('x.sessions', 'y_session');
            $qb->join('y_session.course', 'y_course');
            $qb->andWhere($qb->expr()->in('y_course.year', ':academicYears'));
            $qb->setParameter(':academicYears', $ids);
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
        unset($criteria['academicYears']);

        $this->attachClosingCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
    }

    public function import(array $data, string $type, array $referenceMap): array
    {
        return match ($type) {
            DefaultDataImporter::SESSION_TYPE => $this->importSessionType($data, $type, $referenceMap),
            DefaultDataImporter::SESSION_TYPE_X_AAMC_METHOD
                => $this->importSessionTypeToMethodMapping($data, $referenceMap),
            default => throw new Exception("Unable to import data of type $type."),
        };
    }

    protected function importSessionType(array $data, string $type, array $referenceMap): array
    {
        // `session_type_id`,`title`,`school_id`,`calendar_color`,`assessment`,`assessment_option_id`, `active`
        $entity = new SessionType();
        $entity->setId($data[0]);
        $entity->setTitle($data[1]);
        $entity->setSchool($referenceMap[DefaultDataImporter::SCHOOL . $data[2]]);
        $entity->setCalendarColor($data[3]);
        $entity->setAssessment($data[4]);
        $entity->setActive($data[6]);
        if (! empty($data[5])) {
            $entity->setAssessmentOption($referenceMap[DefaultDataImporter::ASSESSMENT_OPTION . $data[5]]);
        }
        $this->importEntity($entity);
        $referenceMap[$type . $entity->getId()] = $entity;
        return $referenceMap;
    }

    protected function importSessionTypeToMethodMapping(array $data, array $referenceMap): array
    {
        /** @var SessionType $entity */
        $entity = $referenceMap[DefaultDataImporter::SESSION_TYPE . $data[0]];
        $entity->addAamcMethod($referenceMap[DefaultDataImporter::AAMC_METHOD . $data[1]]);
        $this->update($entity, true, true);
        return $referenceMap;
    }
}
