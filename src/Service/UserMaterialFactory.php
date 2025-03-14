<?php

declare(strict_types=1);

namespace App\Service;

use App\Classes\UserMaterial;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RouterInterface;

class UserMaterialFactory
{
    public function __construct(protected RouterInterface $router)
    {
    }

    public function create(array $material): UserMaterial
    {
        if (array_key_exists('filename', $material) && !empty($material['filename'])) {
            $absoluteFileUri = $this->router->generate(
                'app_download_downloadmaterials',
                ['token' => $material['token']],
                UrlGenerator::ABSOLUTE_URL
            );
        }

        /** @var UserMaterial $obj */
        $obj = new UserMaterial();
        $obj->id = $material['id'];
        $obj->courseLearningMaterial = $material['clmId'] ?? null;
        $obj->sessionLearningMaterial = $material['slmId'] ?? null;
        $obj->position = $material['position'] ?? null;
        $obj->session = $material['sessionId'] ?? null;
        $obj->course = $material['courseId'] ?? null;
        $obj->sessionTitle = $material['sessionTitle'] ?? null;
        $obj->courseTitle = $material['courseTitle'] ?? null;
        $obj->courseExternalId = $material['courseExternalId'] ?? null;
        $obj->courseYear = (int) $material['courseYear'];
        $obj->firstOfferingDate = $material['firstOfferingDate'] ?? null;
        $obj->instructors = $material['instructors'] ?? [];
        if ($material['publicNotes']) {
            $obj->publicNotes = $material['notes'];
        }
        $obj->required = $material['required'];
        $obj->title = $material['title'];
        $obj->description = $material['description'];
        $obj->originalAuthor = $material['originalAuthor'];
        $obj->absoluteFileUri = $absoluteFileUri ?? null;
        $obj->citation = $material['citation'];
        $obj->link = $material['link'];
        $obj->filename = $material['filename'];
        $obj->filesize = $material['filesize'];
        $obj->mimetype = $material['mimetype'];
        $obj->startDate = $material['startDate'];
        $obj->endDate = $material['endDate'];
        $obj->status = (int) $material['status'];
        $obj->isBlanked = false;

        return $obj;
    }
}
