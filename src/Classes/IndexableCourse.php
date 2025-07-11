<?php

declare(strict_types=1);

namespace App\Classes;

use App\Entity\DTO\CourseDTO;

class IndexableCourse
{
    public CourseDTO $courseDTO;

    public string $school;

    public int $schoolId;

    public ?string $clerkshipType;

    public array $directors = [];

    public array $administrators = [];

    public array $terms = [];

    public array $objectives = [];

    public array $meshDescriptorIds = [];

    public array $meshDescriptorNames = [];

    public array $meshDescriptorAnnotations = [];

    public array $learningMaterialTitles = [];

    public array $learningMaterialDescriptions = [];

    public array $learningMaterialCitations = [];

    public array $fileLearningMaterialIds = [];

    /** @var IndexableSession[]  */
    public array $sessions = [];

    public function createIndexObjects(): array
    {
        $courseData = [
            'courseId' => $this->courseDTO->id,
            'school' => $this->school,
            'schoolId' => $this->schoolId,
            'courseYear' => $this->courseDTO->year,
            'courseTitle' => $this->courseDTO->title,
            'courseExternalId' => $this->courseDTO->externalId,
            'clerkshipType' => $this->clerkshipType,
            'courseDirectors' => implode(' ', $this->directors),
            'courseAdministrators' => implode(' ', $this->administrators),
            'courseObjectives' => implode(' ', $this->objectives),
            'courseTerms' => implode(' ', $this->terms),
            'courseMeshDescriptorIds' => array_values($this->meshDescriptorIds),
            'courseMeshDescriptorNames' => array_values($this->meshDescriptorNames),
            'courseMeshDescriptorAnnotations' => implode(' ', $this->meshDescriptorAnnotations),
            'courseLearningMaterialTitles' => array_values($this->learningMaterialTitles),
            'courseLearningMaterialDescriptions' => array_values($this->learningMaterialDescriptions),
            'courseLearningMaterialCitations' => array_values($this->learningMaterialCitations),
            'courseLearningMaterialAttachments' => [],
            'courseFileLearningMaterialIds' => array_values($this->fileLearningMaterialIds),
        ];

        return array_map(function (IndexableSession $session) use ($courseData) {
            $sessionData = $session->createIndexObject();

            return array_merge($courseData, $sessionData);
        }, $this->sessions);
    }
}
