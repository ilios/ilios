<?php
namespace Ilios\CoreBundle\Service;

use Ilios\CoreBundle\Classes\UserMaterial;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGenerator;

class UserMaterialFactory
{

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var string
     */
    protected $decoratorClassName;

    /**
     * @param Router $router
     * @param string $decoratorClassName
     */
    public function __construct(Router $router, $decoratorClassName)
    {
        $this->router = $router;
        $this->decoratorClassName = $decoratorClassName;
    }

    /**
     * @param array $material
     * @return UserMaterial
     */
    public function create(
        array $material
    ) {
        if (array_key_exists('filename', $material) && !empty($material['filename'])) {
            $absoluteFileUri = $this->router->generate(
                'ilios_core_downloadlearningmaterial',
                ['token' => $material['token']],
                UrlGenerator::ABSOLUTE_URL
            );
        }

        /* @var UserMaterial $obj */
        $obj = new $this->decoratorClassName();
        $obj->id = $material['id'];
        $obj->courseLearningMaterial = isset($material['clmId'])?$material['clmId']:null;
        $obj->sessionLearningMaterial = isset($material['slmId'])?$material['slmId']:null;
        $obj->position = isset($material['position'])?$material['position']:null;
        $obj->session = isset($material['sessionId'])?$material['sessionId']:null;
        $obj->course = isset($material['courseId'])?$material['courseId']:null;
        $obj->sessionTitle = isset($material['sessionTitle'])?$material['sessionTitle']:null;
        $obj->courseTitle = isset($material['courseTitle'])?$material['courseTitle']:null;
        $obj->firstOfferingDate = isset($material['firstOfferingDate'])?$material['firstOfferingDate']:null;
        $obj->instructors = isset($material['instructors'])?$material['instructors']:[];
        if ($material['publicNotes']) {
            $obj->publicNotes = $material['notes'];
        }
        $obj->required = $material['required'];
        $obj->title = $material['title'];
        $obj->description = $material['description'];
        $obj->originalAuthor = $material['originalAuthor'];
        $obj->absoluteFileUri = isset($absoluteFileUri)?$absoluteFileUri:null;
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
