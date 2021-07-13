<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\DTO\LearningMaterialDTO;
use App\Traits\StringableEntityInterface;
use App\Entity\LearningMaterialInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RouterInterface;

class LearningMaterialDecoratorFactory
{

    /**
     * @var RouterInterface
     */
    protected $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param mixed $object
     * @return LearningMaterialDTO
     */
    public function create($object)
    {
        if (!$object instanceof LearningMaterialInterface && !$object instanceof LearningMaterialDTO) {
            throw new \InvalidArgumentException(
                "Object must by a learning material entity or DTO got " . $object::class
            );
        }
        if ($object instanceof LearningMaterialInterface) {
            $object = $this->entityToDto($object);
        }

        return $this->decorateDto($object);
    }

    /**
     * @return LearningMaterialDTO
     */
    protected function entityToDto(LearningMaterialInterface $learningMaterial)
    {
        $dto = new LearningMaterialDTO(
            $learningMaterial->getId(),
            $learningMaterial->getTitle(),
            $learningMaterial->getDescription(),
            $learningMaterial->getUploadDate(),
            $learningMaterial->getOriginalAuthor(),
            $learningMaterial->getCitation(),
            $learningMaterial->hasCopyrightPermission(),
            $learningMaterial->getCopyrightRationale(),
            $learningMaterial->getFilename(),
            $learningMaterial->getMimetype(),
            $learningMaterial->getFilesize(),
            $learningMaterial->getLink(),
            $learningMaterial->getToken(),
            $learningMaterial->getRelativePath()
        );
        $dto->userRole = $learningMaterial->getUserRole()->getId();
        $dto->owningUser = $learningMaterial->getOwningUser()->getId();
        $dto->status = $learningMaterial->getStatus()->getId();
        $dto->courseLearningMaterials = $learningMaterial->getCourseLearningMaterials()
            ->map(function (StringableEntityInterface $courseLearningMaterial) {
                return (string) $courseLearningMaterial;
            })->toArray();
        $dto->sessionLearningMaterials = $learningMaterial->getSessionLearningMaterials()
            ->map(function (StringableEntityInterface $sessionLearningMaterial) {
                return (string) $sessionLearningMaterial;
            })->toArray();

        return $dto;
    }

    /**
     * @return LearningMaterialDTO
     */
    protected function decorateDto(LearningMaterialDTO $learningMaterialDTO)
    {
        if ($learningMaterialDTO->filename) {
            $link = $this->router->generate(
                'ilios_downloadlearningmaterial',
                ['token' => $learningMaterialDTO->token],
                UrlGenerator::ABSOLUTE_URL
            );
            $learningMaterialDTO->absoluteFileUri = $link;
        }

        return $learningMaterialDTO;
    }
}
