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
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

/**
 * Applies a factory to decorate the entity or DTO before it is sent
 */
class FactoryNormalizer implements ContextAwareNormalizerInterface, NormalizationAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'FACTORY_NORMALIZER_ALREADY_CALLED';

    public function __construct(
        protected LearningMaterialDecoratorFactory $learningMaterialDecoratorFactory,
        protected CurriculumInventoryReportDecoratorFactory $curriculumInventoryReportDecoratorFactory
    ) {
    }

    public function normalize(
        $o,
        string $format = null,
        array $context = [],
    ): array|string|int|float|bool|ArrayObject|null {
        $class = $o::class;
        $o = match ($class) {
            LearningMaterial::class, LearningMaterialDTO::class => $this->learningMaterialDecoratorFactory->create($o),
            CurriculumInventoryReportDTO::class => $this->curriculumInventoryReportDecoratorFactory->create($o),
            default => throw new Exception("${class} fell through match statement, should it have been decorated?"),
        };

        $context[self::ALREADY_CALLED] = true;
        return $this->normalizer->normalize($o, $format, $context);
    }

    /*
     * Since we call upon the normalizer chain here we have to avoid recursion by examining
     * the context to avoid calling ourselves again.
     */
    public function supportsNormalization($classNameOrObject, string $format = null, array $context = []): bool
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
        $class = is_object($classNameOrObject) ? $classNameOrObject::class : $classNameOrObject;
        return in_array($class, $decoratedTypes);
    }
}
