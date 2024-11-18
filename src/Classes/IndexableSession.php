<?php

declare(strict_types=1);

namespace App\Classes;

use App\Service\Index\Curriculum;

class IndexableSession
{
    public int $courseId;

    public int $sessionId;

    public string $title;

    public string $sessionType;

    public ?string $description;

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

    public function createIndexObject(): array
    {
        return [
            'id' => Curriculum::SESSION_ID_PREFIX . $this->sessionId,
            'sessionId' => $this->sessionId,
            'sessionTitle' => $this->title,
            'sessionType' => $this->sessionType,
            'sessionDescription' => $this->description,
            'sessionAdministrators' => implode(' ', $this->administrators),
            'sessionObjectives' => implode(' ', $this->objectives),
            'sessionTerms' => implode(' ', $this->terms),
            'sessionMeshDescriptorIds' => array_values($this->meshDescriptorIds),
            'sessionMeshDescriptorNames' => array_values($this->meshDescriptorNames),
            'sessionMeshDescriptorAnnotations' => implode(' ', $this->meshDescriptorAnnotations),
            'sessionLearningMaterialTitles' => array_values($this->learningMaterialTitles),
            'sessionLearningMaterialDescriptions' => array_values($this->learningMaterialDescriptions),
            'sessionLearningMaterialCitations' => array_values($this->learningMaterialCitations),
            'sessionLearningMaterialAttachments' => [],
            'sessionFileLearningMaterialIds' => array_values($this->fileLearningMaterialIds),
        ];
    }
}
