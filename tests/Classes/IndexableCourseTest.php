<?php

declare(strict_types=1);

namespace App\Tests\Classes;

use App\Classes\IndexableCourse;
use App\Classes\IndexableSession;
use App\Entity\DTO\CourseDTO;
use DateTime;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IndexableCourse::class)]
class IndexableCourseTest extends TestCase
{
    public function testCreateIndexObjects(): void
    {
        $indexableCourse = new IndexableCourse();
        $indexableCourse->courseDTO = new CourseDTO(
            1,
            'test course 1',
            4,
            2024,
            new DateTime('2024-04-01 00:00:00'),
            new DateTime('2024-04-22 15:24:22'),
            'ILIOS1',
            true,
            false,
            false,
            true,
        );
        $indexableCourse->school = 'School of Medicine';
        $indexableCourse->clerkshipType = 'Longitudinal';
        $indexableCourse->directors = ['Danny Director', 'Deidre Sr. Director'];
        $indexableCourse->administrators = ['Anna Administator', 'Alfonse Admin'];
        $indexableCourse->terms = ['lorem', 'ipsum'];
        $indexableCourse->objectives = ['course objective 1', 'courssdfadssdfasdf'];
        $indexableCourse->meshDescriptorIds = [1001, 1002];
        $indexableCourse->meshDescriptorAnnotations = ['brain', 'other brain'];
        $indexableCourse->meshDescriptorNames = ['mandatory', 'good to have'];
        $indexableCourse->learningMaterialTitles = ['lm citation', 'lm file'];
        $indexableCourse->learningMaterialDescriptions = ['this is a citation', null];
        $indexableCourse->learningMaterialCitations = ['ilse billse keiner willse', null];
        $indexableCourse->fileLearningMaterialIds = [201];

        $indexableSession1 = new IndexableSession();
        $indexableSession1->courseId = $indexableCourse->courseDTO->id;
        $indexableSession1->sessionId = 1;
        $indexableSession1->title = 'Session 1';
        $indexableSession1->sessionType = 'Lecture';
        $indexableSession1->description = null;
        $indexableSession1->administrators = ['Session Admin 1', 'Session Admin 2'];
        $indexableSession1->terms = ['term 4', 'term ab'];
        $indexableSession1->objectives = ['howdy', 'hello'];
        $indexableSession1->meshDescriptorIds = [20024, 20203];
        $indexableSession1->meshDescriptorNames = ['name', 'other name'];
        $indexableSession1->meshDescriptorAnnotations = [null];
        $indexableSession1->learningMaterialTitles = ['session lm 1 title', 'session lm 1 other title'];
        $indexableSession1->learningMaterialDescriptions = ['whatever', 'lorem ipsum'];
        $indexableSession1->learningMaterialCitations = ['something'];
        $indexableSession1->fileLearningMaterialIds = [444];

        $indexableSession2 = new IndexableSession();
        $indexableSession2->courseId = $indexableCourse->courseDTO->id;
        $indexableSession2->sessionId = 2;
        $indexableSession2->title = 'Session 2';
        $indexableSession2->sessionType = 'small groups';
        $indexableSession2->description = 'learn something';
        $indexableSession2->administrators = ['Session Admin 3', 'Session Admin 4'];
        $indexableSession2->terms = ['term 222', 'term zz'];
        $indexableSession2->objectives = ['poka poka', 'good bye'];
        $indexableSession2->meshDescriptorIds = [12345, 12349];
        $indexableSession2->meshDescriptorNames = ['descriptor', 'other descriptor'];
        $indexableSession2->meshDescriptorAnnotations = ['annotate this!'];
        $indexableSession2->learningMaterialTitles = ['session lm 2 title', 'session lm 2 other title'];
        $indexableSession2->learningMaterialDescriptions = ['super', 'duper'];
        $indexableSession2->learningMaterialCitations = ['stop this madness at once!'];
        $indexableSession2->fileLearningMaterialIds = [324, 4321];

        $indexableSessions = [$indexableSession1, $indexableSession2];

        $indexableCourse->sessions = $indexableSessions;

        $indexObjects = $indexableCourse->createIndexObjects();
        $this->assertCount(2, $indexObjects);

        foreach ($indexObjects as $i => $indexObject) {
            $indexableSession = $indexableSessions[$i];
            $this->assertCount(34, array_keys($indexObject));
            $this->assertEquals($indexableCourse->courseDTO->id, $indexObject['courseId']);
            $this->assertEquals($indexableCourse->school, $indexObject['school']);
            $this->assertEquals($indexableCourse->courseDTO->year, $indexObject['courseYear']);
            $this->assertEquals($indexableCourse->courseDTO->title, $indexObject['courseTitle']);
            $this->assertEquals($indexableCourse->courseDTO->externalId, $indexObject['courseExternalId']);
            $this->assertEquals($indexableCourse->clerkshipType, $indexObject['clerkshipType']);
            $this->assertEquals(implode(' ', $indexableCourse->directors), $indexObject['courseDirectors']);
            $this->assertEquals(implode(' ', $indexableCourse->administrators), $indexObject['courseAdministrators']);
            $this->assertEquals(implode(' ', $indexableCourse->objectives), $indexObject['courseObjectives']);
            $this->assertEquals(implode(' ', $indexableCourse->terms), $indexObject['courseTerms']);
            $this->assertEquals($indexableCourse->meshDescriptorIds, $indexObject['courseMeshDescriptorIds']);
            $this->assertEquals($indexableCourse->meshDescriptorNames, $indexObject['courseMeshDescriptorNames']);
            $this->assertEquals(
                implode(' ', $indexableCourse->meshDescriptorAnnotations),
                $indexObject['courseMeshDescriptorAnnotations']
            );
            $this->assertEquals($indexableCourse->learningMaterialTitles, $indexObject['courseLearningMaterialTitles']);
            $this->assertEquals(
                $indexableCourse->learningMaterialDescriptions,
                $indexObject['courseLearningMaterialDescriptions']
            );
            $this->assertEquals(
                $indexableCourse->learningMaterialCitations,
                $indexObject['courseLearningMaterialCitations']
            );
            // Indexing of attachments is not implemented, this is hardwired to an empty array
            $this->assertEquals([], $indexObject['courseLearningMaterialAttachments']);
            $this->assertEquals(
                $indexableCourse->fileLearningMaterialIds,
                $indexObject['courseFileLearningMaterialIds']
            );
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

    public function testCreateIndexObjectsWithoutIndexableSessions(): void
    {
        $indexableCourse = new IndexableCourse();
        $indexableCourse->courseDTO = new CourseDTO(
            1,
            'test course 1',
            4,
            2024,
            new DateTime('2024-04-01 00:00:00'),
            new DateTime('2024-04-22 15:24:22'),
            'ILIOS1',
            true,
            false,
            false,
            true,
        );
        $indexableCourse->school = 'school of whatever';
        $indexableCourse->clerkshipType = null;
        $this->assertEmpty($indexableCourse->createIndexObjects());
    }
}
