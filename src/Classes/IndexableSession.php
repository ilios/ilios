<?php

namespace App\Classes;

class IndexableSession
{
    /** @var int */
    public $courseId;

    /** @var int */
    public $sessionId;

    /** @var string */
    public $title;

    /** @var string */
    public $sessionType;

    /** @var string */
    public $description;

    /** @var array  */
    public $directors = [];

    /** @var array  */
    public $administrators = [];

    /** @var array  */
    public $terms = [];

    /** @var array  */
    public $objectives = [];

    /** @var array  */
    public $meshDescriptors = [];

    /** @var array  */
    public $learningMaterials = [];

    public function createIndexObject()
    {
        return [
            'id' => ElasticSearchBase::SESSION_ID_PREFIX . $this->sessionId,
            'sessionId' => $this->sessionId,
            'sessionTitle' => $this->title,
            'sessionType' => $this->sessionType,
            'sessionDescription' => $this->description,
            'sessionAdministrators' => implode(' ', $this->administrators),
            'sessionObjectives' => implode(' ', $this->objectives),
            'sessionTerms' => implode(' ', $this->terms),
            'sessionMeshDescriptors' => implode(' ', $this->meshDescriptors),
            'sessionLearningMaterials' => implode(' ', $this->learningMaterials),
        ];
    }
}
