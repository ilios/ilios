<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Form\DataTransformer\RemoveMarkupTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SessionTypeType
 * @package Ilios\CoreBundle\Form\Type
 */
class SessionTypeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, ['empty_data' => null])
            ->add('sessionTypeCssClass', null, ['required' => false, 'empty_data' => null])
            ->add('assessment', null, ['required' => false])
            ->add('assessmentOption', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:AssessmentOption"
            ])
            ->add('school', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:School"
            ])
            ->add('aamcMethods', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:AamcMethod"
            ])
        ;

        $transformer = new RemoveMarkupTransformer();
        foreach (['title', 'sessionTypeCssClass'] as $element) {
            $builder->get($element)->addViewTransformer($transformer);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\SessionType'
        ));
    }
}
