<?php
namespace AppBundle\Service;

use AppBundle\Entity\DTO\LearningMaterialDTO;
use AppBundle\Traits\StringableEntityInterface;
use AppBundle\Entity\LearningMaterialInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RouterInterface;

class LearningMaterialDecoratorFactory
{

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @param RouterInterface $router
     */
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
                "Object must by a learning material entity or DTO got " . get_class($object)
            );
        }
        if ($object instanceof LearningMaterialInterface) {
            $object = $this->entityToDto($object);
        }

        return $this->decorateDto($object);
    }

    /**
     * @param LearningMaterialInterface $learningMaterial
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
            $learningMaterial->getToken()
        );
        $dto->userRole = $learningMaterial->getUserRole()->getId();
        $dto->owningUser = $learningMaterial->getOwningUser()->getId();
        $dto->status = $learningMaterial->getStatus()->getId();
        $dto->courseLearningMaterials = $learningMaterial->getCourseLearningMaterials()
            ->map(function (StringableEntityInterface $courseLearningMaterial) {
                return (string) $courseLearningMaterial;
            });
        $dto->sessionLearningMaterials = $learningMaterial->getSessionLearningMaterials()
            ->map(function (StringableEntityInterface $sessionLearningMaterial) {
                return (string) $sessionLearningMaterial;
            });

        return $dto;
    }

    /**
     * @param LearningMaterialDTO $learningMaterialDTO
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
