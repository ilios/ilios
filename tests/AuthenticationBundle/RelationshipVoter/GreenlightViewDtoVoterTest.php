<?php

namespace Tests\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\RelationshipVoter\AbstractVoter;
use Ilios\AuthenticationBundle\RelationshipVoter\GreenlightViewDTOVoter as Voter;
use Ilios\AuthenticationBundle\Service\PermissionChecker;
use Ilios\CoreBundle\Entity\DTO\AamcMethodDTO;
use Ilios\CoreBundle\Entity\DTO\AamcPcrsDTO;
use Ilios\CoreBundle\Entity\DTO\AamcResourceTypeDTO;
use Ilios\CoreBundle\Entity\DTO\AssessmentOptionDTO;
use Ilios\CoreBundle\Entity\DTO\CohortDTO;
use Ilios\CoreBundle\Entity\DTO\CompetencyDTO;
use Ilios\CoreBundle\Entity\DTO\CourseClerkshipTypeDTO;
use Ilios\CoreBundle\Entity\DTO\CourseDTO;
use Ilios\CoreBundle\Entity\DTO\CurriculumInventoryAcademicLevelDTO;
use Ilios\CoreBundle\Entity\DTO\CurriculumInventoryInstitutionDTO;
use Ilios\CoreBundle\Entity\DTO\CurriculumInventoryReportDTO;
use Ilios\CoreBundle\Entity\DTO\CurriculumInventorySequenceBlockDTO;
use Ilios\CoreBundle\Entity\DTO\CurriculumInventorySequenceDTO;
use Ilios\CoreBundle\Entity\DTO\DepartmentDTO;
use Ilios\CoreBundle\Entity\DTO\IlmSessionDTO;
use Ilios\CoreBundle\Entity\DTO\InstructorGroupDTO;
use Ilios\CoreBundle\Entity\DTO\LearningMaterialStatusDTO;
use Ilios\CoreBundle\Entity\DTO\MeshConceptDTO;
use Ilios\CoreBundle\Entity\DTO\MeshDescriptorDTO;
use Ilios\CoreBundle\Entity\DTO\MeshPreviousIndexingDTO;
use Ilios\CoreBundle\Entity\DTO\MeshQualifierDTO;
use Ilios\CoreBundle\Entity\DTO\MeshTermDTO;
use Ilios\CoreBundle\Entity\DTO\MeshTreeDTO;
use Ilios\CoreBundle\Entity\DTO\ObjectiveDTO;
use Ilios\CoreBundle\Entity\DTO\ProgramDTO;
use Ilios\CoreBundle\Entity\DTO\ProgramYearDTO;
use Ilios\CoreBundle\Entity\DTO\ProgramYearStewardDTO;
use Ilios\CoreBundle\Entity\DTO\SchoolDTO;
use Ilios\CoreBundle\Entity\DTO\SessionDescriptionDTO;
use Ilios\CoreBundle\Entity\DTO\SessionDTO;
use Ilios\CoreBundle\Entity\DTO\SessionTypeDTO;
use Ilios\CoreBundle\Entity\DTO\TermDTO;
use Ilios\CoreBundle\Entity\DTO\VocabularyDTO;
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
            [CurriculumInventoryAcademicLevelDTO::class],
            [CurriculumInventoryInstitutionDTO::class],
            [CurriculumInventoryReportDTO::class],
            [CurriculumInventorySequenceDTO::class],
            [CurriculumInventorySequenceBlockDTO::class],
            [DepartmentDTO::class],
            [IlmSessionDTO::class],
            [InstructorGroupDTO::class],
            [LearningMaterialStatusDTO::class],
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
            [SessionDTO::class],
            [SessionDescriptionDTO::class],
            [SessionTypeDTO::class],
            [TermDTO::class],
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
