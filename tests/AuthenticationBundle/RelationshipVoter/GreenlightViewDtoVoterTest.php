<?php

namespace Tests\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\RelationshipVoter\AbstractVoter;
use Ilios\AuthenticationBundle\RelationshipVoter\GreenlightViewDTOVoter as Voter;
use AppBundle\Service\PermissionChecker;
use AppBundle\Entity\DTO\AamcMethodDTO;
use AppBundle\Entity\DTO\AamcPcrsDTO;
use AppBundle\Entity\DTO\AamcResourceTypeDTO;
use AppBundle\Entity\DTO\AssessmentOptionDTO;
use AppBundle\Entity\DTO\CohortDTO;
use AppBundle\Entity\DTO\CompetencyDTO;
use AppBundle\Entity\DTO\CourseClerkshipTypeDTO;
use AppBundle\Entity\DTO\CourseDTO;
use AppBundle\Entity\DTO\CourseLearningMaterialDTO;
use AppBundle\Entity\DTO\CurriculumInventoryAcademicLevelDTO;
use AppBundle\Entity\DTO\CurriculumInventoryInstitutionDTO;
use AppBundle\Entity\DTO\CurriculumInventoryReportDTO;
use AppBundle\Entity\DTO\CurriculumInventorySequenceBlockDTO;
use AppBundle\Entity\DTO\CurriculumInventorySequenceDTO;
use AppBundle\Entity\DTO\DepartmentDTO;
use AppBundle\Entity\DTO\IlmSessionDTO;
use AppBundle\Entity\DTO\InstructorGroupDTO;
use AppBundle\Entity\DTO\LearningMaterialDTO;
use AppBundle\Entity\DTO\LearningMaterialStatusDTO;
use AppBundle\Entity\DTO\LearningMaterialUserRoleDTO;
use AppBundle\Entity\DTO\MeshConceptDTO;
use AppBundle\Entity\DTO\MeshDescriptorDTO;
use AppBundle\Entity\DTO\MeshPreviousIndexingDTO;
use AppBundle\Entity\DTO\MeshQualifierDTO;
use AppBundle\Entity\DTO\MeshTermDTO;
use AppBundle\Entity\DTO\MeshTreeDTO;
use AppBundle\Entity\DTO\ObjectiveDTO;
use AppBundle\Entity\DTO\ProgramDTO;
use AppBundle\Entity\DTO\ProgramYearDTO;
use AppBundle\Entity\DTO\ProgramYearStewardDTO;
use AppBundle\Entity\DTO\SchoolConfigDTO;
use AppBundle\Entity\DTO\SchoolDTO;
use AppBundle\Entity\DTO\SessionDescriptionDTO;
use AppBundle\Entity\DTO\SessionDTO;
use AppBundle\Entity\DTO\SessionLearningMaterialDTO;
use AppBundle\Entity\DTO\SessionTypeDTO;
use AppBundle\Entity\DTO\TermDTO;
use AppBundle\Entity\DTO\UserRoleDTO;
use AppBundle\Entity\DTO\VocabularyDTO;
use AppBundle\Service\Config;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Class GreenlightViewDtoVoterTest
 * @package Tests\AuthenticationBundle\RelationshipVoter
 */
class GreenlightViewDtoVoterTest extends AbstractBase
{
    /**
     * @inheritdoc
     */
    public function setup()
    {
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker);
    }

    public function canViewDTOProvider()
    {
        return [
            [AamcMethodDTO::class],
            [AamcPcrsDTO::class],
            [AamcResourceTypeDTO::class],
            [AssessmentOptionDTO::class],
            [CohortDTO::class],
            [CompetencyDTO::class],
            [CourseDTO::class],
            [CourseClerkshipTypeDTO::class],
            [CourseLearningMaterialDTO::class],
            [CurriculumInventoryAcademicLevelDTO::class],
            [CurriculumInventoryInstitutionDTO::class],
            [CurriculumInventoryReportDTO::class],
            [CurriculumInventorySequenceDTO::class],
            [CurriculumInventorySequenceBlockDTO::class],
            [DepartmentDTO::class],
            [IlmSessionDTO::class],
            [InstructorGroupDTO::class],
            [LearningMaterialDTO::class],
            [LearningMaterialStatusDTO::class],
            [LearningMaterialUserRoleDTO::class],
            [MeshConceptDTO::class],
            [MeshDescriptorDTO::class],
            [MeshPreviousIndexingDTO::class],
            [MeshQualifierDTO::class],
            [MeshTermDTO::class],
            [MeshTreeDTO::class],
            [ObjectiveDTO::class],
            [ProgramDTO::class],
            [ProgramYearDTO::class],
            [ProgramYearStewardDTO::class],
            [SchoolDTO::class],
            [SchoolConfigDTO::class],
            [SessionDTO::class],
            [SessionDescriptionDTO::class],
            [SessionLearningMaterialDTO::class],
            [SessionTypeDTO::class],
            [TermDTO::class],
            [UserRoleDTO::class],
            [VocabularyDTO::class],
        ];
    }

    /**
     * @dataProvider canViewDTOProvider
     * @covers GreenlightViewDTOVoter::voteOnAttribute()
     * @param string $class The fully qualified class name.
     */
    public function testCanViewDTO($class)
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $dto = m::mock($class);
        $response = $this->voter->vote($token, $dto, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "DTO View allowed");
    }
}
