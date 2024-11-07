<?php

declare(strict_types=1);

namespace App\Normalizer;

use App\Entity\DTO\CurriculumInventoryReportDTO;
use App\Entity\DTO\LearningMaterialDTO;
use App\Entity\LearningMaterial;
use App\Service\CurriculumInventoryReportDecoratorFactory;
use App\Service\LearningMaterialDecoratorFactory;
use ArrayObject;
use Exception;
use Symfony\Component\Serializer\Encoder\NormalizationAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Applies a factory to decorate the entity or DTO before it is sent
 */
class FactoryNormalizer implements NormalizerInterface, NormalizationAwareInterface
{
    use NormalizerAwareTrait;

    private const string ALREADY_CALLED = 'FACTORY_NORMALIZER_ALREADY_CALLED';

    public function __construct(
        protected LearningMaterialDecoratorFactory $learningMaterialDecoratorFactory,
        protected CurriculumInventoryReportDecoratorFactory $curriculumInventoryReportDecoratorFactory
    ) {
    }

    public function normalize(
        mixed $object,
        ?string $format = null,
        array $context = [],
    ): array|string|int|float|bool|ArrayObject|null {
        $class = $object::class;
        $object = match ($class) {
            LearningMaterial::class, LearningMaterialDTO::class =>
            $this->learningMaterialDecoratorFactory->create($object),
            CurriculumInventoryReportDTO::class => $this->curriculumInventoryReportDecoratorFactory->create($object),
            default => throw new Exception("{$class} fell through match statement, should it have been decorated?"),
        };

        $context[self::ALREADY_CALLED] = true;
        return $this->normalizer->normalize($object, $format, $context);
    }

    /*
     * Since we call upon the normalizer chain here we have to avoid recursion by examining
     * the context to avoid calling ourselves again.
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        if (!in_array($format, ['json', 'json-api'])) {
            return false;
        }

        $decoratedTypes = [
            LearningMaterial::class,
            LearningMaterialDTO::class,
            CurriculumInventoryReportDTO::class,
        ];
        $class = is_object($data) ? $data::class : $data;
        return in_array($class, $decoratedTypes);
    }

    /**
     * For the most part we cannot cache normalization of any types because we rely on the $context
     * in our supportsNormalization method. However, when the $format isn't supported we can cache that.
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            '*' => !in_array($format, ['json', 'json-api']),
        ];
    }
}
