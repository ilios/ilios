<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\DTO\LearningMaterialDTO;
use App\Entity\LearningMaterialInterface;
use InvalidArgumentException;
use Stringable;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RouterInterface;

class LearningMaterialDecoratorFactory
{
    public function __construct(protected RouterInterface $router)
    {
    }

    public function create(mixed $object): LearningMaterialDTO
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
            $learningMaterial->getUserRole()->getId(),
            $learningMaterial->getStatus()->getId(),
            $learningMaterial->getOwningUser()->getId(),
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
        $dto->courseLearningMaterials = $learningMaterial->getCourseLearningMaterials()->map(
            fn(Stringable $courseLearningMaterial) => (string) $courseLearningMaterial
        )->toArray();
        $dto->sessionLearningMaterials = $learningMaterial->getSessionLearningMaterials()->map(
            fn(Stringable $sessionLearningMaterial) => (string) $sessionLearningMaterial
        )->toArray();

        return $dto;
    }

    protected function decorateDto(LearningMaterialDTO $learningMaterialDTO): LearningMaterialDTO
    {
        if ($learningMaterialDTO->filename) {
            $link = $this->router->generate(
                'app_download_downloadmaterials',
                ['token' => $learningMaterialDTO->token],
                UrlGenerator::ABSOLUTE_URL
            );
            $learningMaterialDTO->absoluteFileUri = $link;
        }

        return $learningMaterialDTO;
    }
}
