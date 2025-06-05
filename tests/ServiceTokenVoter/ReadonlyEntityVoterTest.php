<?php

declare(strict_types=1);

namespace App\Tests\ServiceTokenVoter;

use App\Entity\AamcMethodInterface;
use App\Entity\AamcPcrsInterface;
use App\Entity\AamcResourceTypeInterface;
use App\Entity\AssessmentOptionInterface;
use App\Entity\CourseClerkshipTypeInterface;
use App\Entity\CurriculumInventoryAcademicLevelInterface;
use App\Entity\IngestionExceptionInterface;
use App\Entity\LearningMaterialInterface;
use App\Entity\LearningMaterialStatusInterface;
use App\Entity\LearningMaterialUserRoleInterface;
use App\Entity\MeshConceptInterface;
use App\Entity\MeshDescriptorInterface;
use App\Entity\MeshPreviousIndexingInterface;
use App\Entity\MeshQualifierInterface;
use App\Entity\MeshTermInterface;
use App\Entity\MeshTreeInterface;
use App\Entity\UserInterface;
use App\Entity\UserRoleInterface;
use App\ServiceTokenVoter\ReadonlyEntityVoter as Voter;

final class ReadonlyEntityVoterTest extends AbstractReadonlyBase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->voter = new Voter();
    }

    public static function subjectProvider(): array
    {
        return [
            [AamcMethodInterface::class],
            [AamcPcrsInterface::class],
            [AamcResourceTypeInterface::class],
            [AssessmentOptionInterface::class],
            [CourseClerkshipTypeInterface::class],
            [CurriculumInventoryAcademicLevelInterface::class],
            [IngestionExceptionInterface::class],
            [LearningMaterialInterface::class],
            [LearningMaterialStatusInterface::class],
            [LearningMaterialUserRoleInterface::class],
            [MeshConceptInterface::class],
            [MeshDescriptorInterface::class],
            [MeshPreviousIndexingInterface::class],
            [MeshQualifierInterface::class],
            [MeshTermInterface::class],
            [MeshTreeInterface::class],
            [UserInterface::class],
            [UserRoleInterface::class],
        ];
    }

    public static function supportsTypeProvider(): array
    {
        return [
            [AamcMethodInterface::class, true],
            [AamcPcrsInterface::class, true],
            [AamcResourceTypeInterface::class, true],
            [AssessmentOptionInterface::class, true],
            [CourseClerkshipTypeInterface::class, true],
            [CurriculumInventoryAcademicLevelInterface::class, true],
            [IngestionExceptionInterface::class, true],
            [LearningMaterialInterface::class, true],
            [LearningMaterialStatusInterface::class, true],
            [LearningMaterialUserRoleInterface::class, true],
            [MeshConceptInterface::class, true],
            [MeshDescriptorInterface::class, true],
            [MeshPreviousIndexingInterface::class, true],
            [MeshQualifierInterface::class, true],
            [MeshTermInterface::class, true],
            [MeshTreeInterface::class, true],
            [UserInterface::class, true],
            [UserRoleInterface::class, true],
            [self::class, false],
        ];
    }
}
