<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Form\DataTransformer\RemoveMarkupTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Ilios\CoreBundle\Form\Type\AbstractType\ManyRelatedType;

/**
 * Class AssessmentOptionType
 * @package Ilios\CoreBundle\Form\Type
 */
class AssessmentOptionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, ['empty_data' => null])
            ->add('sessionTypes', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:SessionType"
            ])
        ;
        $builder->get('name')->addViewTransformer(new RemoveMarkupTransformer());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\AssessmentOption'
        ));
    }
}
