<?php

declare(strict_types=1);

namespace App\Tests\Classes;

use App\Classes\IndexableCourse;
use App\Classes\IndexableSession;
use App\Entity\DTO\CourseDTO;
use DateTime;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IndexableSession::class)]
final class IndexableSessionTest extends TestCase
{
    public function testCreateIndexObjects(): void
    {
        $indexableSession = new IndexableSession();
        $indexableSession->courseId = 5;
        $indexableSession->sessionId = 1;
        $indexableSession->title = 'Session 1';
        $indexableSession->sessionType = 'Lecture';
        $indexableSession->description = null;
        $indexableSession->administrators = ['Session Admin 1', 'Session Admin 2'];
        $indexableSession->terms = ['term 4', 'term ab'];
        $indexableSession->objectives = ['howdy', 'hello'];
        $indexableSession->meshDescriptorIds = [20024, 20203];
        $indexableSession->meshDescriptorNames = ['name', 'other name'];
        $indexableSession->meshDescriptorAnnotations = [null];
        $indexableSession->learningMaterialTitles = ['session lm 1 title', 'session lm 1 other title'];
        $indexableSession->learningMaterialDescriptions = ['whatever', 'lorem ipsum'];
        $indexableSession->learningMaterialCitations = ['something'];
        $indexableSession->fileLearningMaterialIds = [444];

        $indexObject = $indexableSession->createIndexObject();
        $this->assertCount(16, $indexObject);
        $this->assertEquals('session_' . $indexableSession->sessionId, $indexObject['id']);
        $this->assertEquals($indexableSession->sessionId, $indexObject['sessionId']);
        $this->assertEquals($indexableSession->title, $indexObject['sessionTitle']);
        $this->assertEquals($indexableSession->sessionType, $indexObject['sessionType']);
        $this->assertEquals($indexableSession->description, $indexObject['sessionDescription']);
        $this->assertEquals(implode(' ', $indexableSession->administrators), $indexObject['sessionAdministrators']);
        $this->assertEquals(implode(' ', $indexableSession->objectives), $indexObject['sessionObjectives']);
        $this->assertEquals(implode(' ', $indexableSession->terms), $indexObject['sessionTerms']);

        $this->assertEquals($indexableSession->meshDescriptorIds, $indexObject['sessionMeshDescriptorIds']);
        $this->assertEquals($indexableSession->meshDescriptorNames, $indexObject['sessionMeshDescriptorNames']);
        $this->assertEquals(
            implode(' ', $indexableSession->meshDescriptorAnnotations),
            $indexObject['sessionMeshDescriptorAnnotations']
        );
        $this->assertEquals(
            $indexableSession->learningMaterialTitles,
            $indexObject['sessionLearningMaterialTitles'],
        );
        $this->assertEquals(
            $indexableSession->learningMaterialDescriptions,
            $indexObject['sessionLearningMaterialDescriptions']
        );
        $this->assertEquals(
            $indexableSession->learningMaterialCitations,
            $indexObject['sessionLearningMaterialCitations']
        );
        // Indexing of attachments is not implemented, this is hardwired to an empty array
        $this->assertEquals([], $indexObject['sessionLearningMaterialAttachments']);
        $this->assertEquals(
            $indexableSession->fileLearningMaterialIds,
            $indexObject['sessionFileLearningMaterialIds']
        );
    }
}
