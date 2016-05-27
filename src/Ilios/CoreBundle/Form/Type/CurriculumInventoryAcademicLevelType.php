<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Form\DataTransformer\RemoveMarkupTransformer;
use Ilios\CoreBundle\Form\Type\AbstractType\SingleRelatedType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CurriculumInventoryAcademicLevelType
 * @package Ilios\CoreBundle\Form\Type
 */
class CurriculumInventoryAcademicLevelType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, ['empty_data' => null])
            ->add('description', null, ['required' => false, 'empty_data' => null])
            ->add('level')
            ->add('report', SingleRelatedType::class, [
                'required' => true,
                'entityName' => "IliosCoreBundle:CurriculumInventoryReport"
            ])
        ;
        $transformer = new RemoveMarkupTransformer();
        foreach (['name', 'description'] as $element) {
            $builder->get($element)->addViewTransformer($transformer);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevel'
        ));
    }
}
