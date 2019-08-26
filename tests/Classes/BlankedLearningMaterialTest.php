<?php

namespace App\Tests\Classes;

use App\Classes\BlankedLearningMaterial;
use App\Entity\CourseLearningMaterial;
use App\Entity\LearningMaterial;
use App\Entity\LearningMaterialStatus;
use App\Entity\LearningMaterialUserRole;
use App\Entity\School;
use App\Entity\SessionLearningMaterial;
use App\Entity\User;
use App\Tests\TestCase;
use DateTime;
use Mockery as m;

class BlankedLearningMaterialTest extends TestCase
{
    public function testGetters()
    {
        $copyrightPermission = true;
        $courseLearningMaterials = [ new CourseLearningMaterial()];
        $id = 1;
        $owningSchool = new School();
        $owningUser = new User();
        $sessionLearningMaterials = [ new SessionLearningMaterial() ];
        $status = new LearningMaterialStatus();
        $title = 'My Learning Material';
        $uploadDate = new DateTime();
        $userRole = new LearningMaterialUserRole();

        $lm = m::mock(LearningMaterial::class);
        $lm->shouldReceive('hasCopyrightPermission')->andReturn($copyrightPermission);
        $lm->shouldReceive('getCourseLearningMaterials')->andReturn($courseLearningMaterials);
        $lm->shouldReceive('getId')->andReturn($id);
        $lm->shouldReceive('getOwningSchool')->andReturn($owningSchool);
        $lm->shouldReceive('getOwningUser')->andReturn($owningUser);
        $lm->shouldReceive('getSessionLearningMaterials')->andReturn($sessionLearningMaterials);
        $lm->shouldReceive('getStatus')->andReturn($status);
        $lm->shouldReceive('getTitle')->andReturn($title);
        $lm->shouldReceive('getUploadDate')->andReturn($uploadDate);
        $lm->shouldReceive('getUserRole')->andReturn($userRole);

        $lm->shouldNotReceive('getCitation');
        $lm->shouldNotReceive('getDescription');
        $lm->shouldNotReceive('getFilename');
        $lm->shouldNotReceive('getFilesize');
        $lm->shouldNotReceive('getLink');
        $lm->shouldNotReceive('getMimetype');
        $lm->shouldNotReceive('getOriginalAuthor');
        $lm->shouldNotReceive('getRelativePath');
        $lm->shouldNotReceive('getToken');

        $blankedLm = new BlankedLearningMaterial($lm);

        $this->assertEquals($blankedLm->hasCopyrightPermission(), $copyrightPermission);
        $this->assertEquals($blankedLm->getCourseLearningMaterials(), $courseLearningMaterials);
        $this->assertEquals($blankedLm->getId(), $id);
        $this->assertEquals($blankedLm->getOwningSchool(), $owningSchool);
        $this->assertEquals($blankedLm->getOwningUser(), $owningUser);
        $this->assertEquals($blankedLm->getSessionLearningMaterials(), $sessionLearningMaterials);
        $this->assertEquals($blankedLm->getStatus(), $status);
        $this->assertEquals($blankedLm->getTitle(), $title);
        $this->assertEquals($blankedLm->getUploadDate(), $uploadDate);
        $this->assertEquals($blankedLm->getUserRole(), $userRole);

        $this->assertNull($blankedLm->getCitation());
        $this->assertNull($blankedLm->getDescription());
        $this->assertNull($blankedLm->getFilename());
        $this->assertNull($blankedLm->getFilesize());
        $this->assertNull($blankedLm->getLink());
        $this->assertNull($blankedLm->getMimetype());
        $this->assertNull($blankedLm->getOriginalAuthor());
        $this->assertNull($blankedLm->getRelativePath());
        $this->assertNull($blankedLm->getToken());
    }
}
