<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\DTO\LearningMaterialDTO;
use App\Traits\StringableEntityInterface;
use App\Entity\LearningMaterialInterface;
use InvalidArgumentException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RouterInterface;

class LearningMaterialDecoratorFactory
{

    public function __construct(protected RouterInterface $router)
    {
    }

    /**
     * @param mixed $object
     */
    public function create($object): LearningMaterialDTO
    {
        if (!$object instanceof LearningMaterialInterface && !$object instanceof LearningMaterialDTO) {
            throw new InvalidArgumentException(
                "Object must by a learning material entity or DTO got " . $object::class
            );
        }
        if ($object instanceof LearningMaterialInterface) {
            $object = $this->entityToDto($object);
        }

        return $this->decorateDto($object);
    }

    protected function entityToDto(LearningMaterialInterface $learningMaterial): LearningMaterialDTO
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
            ->map(fn(StringableEntityInterface $courseLearningMaterial) => (string) $courseLearningMaterial)->toArray();
        $dto->sessionLearningMaterials = $learningMaterial->getSessionLearningMaterials()
            ->map(
                fn(StringableEntityInterface $sessionLearningMaterial) => (string) $sessionLearningMaterial
            )->toArray();

        return $dto;
    }

    protected function decorateDto(LearningMaterialDTO $learningMaterialDTO): LearningMaterialDTO
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
