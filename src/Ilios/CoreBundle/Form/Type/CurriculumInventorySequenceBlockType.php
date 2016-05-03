<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Form\DataTransformer\RemoveMarkupTransformer;
use Ilios\CoreBundle\Form\Type\AbstractType\SingleRelatedType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CurriculumInventorySequenceBlockType
 * @package Ilios\CoreBundle\Form\Type
 */
class CurriculumInventorySequenceBlockType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, ['empty_data' => null])
            ->add('description', null, ['required' => false, 'empty_data' => null])
            ->add('required', null, ['required' => false])
            ->add('childSequenceOrder', null, ['required' => false])
            ->add('orderInSequence')
            ->add('minimum')
            ->add('maximum')
            ->add('track', null, ['required' => false])
            ->add('startDate', DateTimeType::class, array(
                'widget' => 'single_text',
            ))
            ->add('endDate', DateTimeType::class, array(
                'widget' => 'single_text',
            ))
            ->add('duration')
            ->add('academicLevel', SingleRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:CurriculumInventoryAcademicLevel"
            ])
            ->add('course', SingleRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:Course"
            ])
            ->add('parent', SingleRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:CurriculumInventorySequenceBlock"
            ])
            ->add('report', SingleRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:CurriculumInventoryReport"
            ])
        ;
        $transformer = new RemoveMarkupTransformer();
        foreach (['title', 'description'] as $element) {
            $builder->get($element)->addViewTransformer($transformer);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock'
        ));
    }
}
