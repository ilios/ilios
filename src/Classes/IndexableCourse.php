<?php

namespace App\Classes;

use App\Entity\DTO\CourseDTO;

class IndexableCourse
{
    /** @var CourseDTO */
    public $courseDTO;

    /** @var string */
    public $school;

    /** @var string */
    public $clerkshipType;

    /** @var array  */
    public $directors = [];

    /** @var array  */
    public $administrators = [];

    /** @var array  */
    public $terms = [];

    /** @var array  */
    public $objectives = [];

    /** @var array  */
    public $meshDescriptorIds = [];

    /** @var array  */
    public $meshDescriptorNames = [];

    /** @var array  */
    public $meshDescriptorAnnotations = [];

    /** @var array  */
    public $learningMaterials = [];

    /** @var IndexableSession[]  */
    public $sessions = [];

    public function createIndexObjects()
    {
        $courseData = [
            'courseId' => $this->courseDTO->id,
            'school' => $this->school,
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
            'courseLearningMaterials' => implode(' ', $this->learningMaterials),
        ];

        return array_map(function (IndexableSession $session) use ($courseData) {
            $sessionData = $session->createIndexObject();

            return array_merge($courseData, $sessionData);
        }, $this->sessions);
    }
}
