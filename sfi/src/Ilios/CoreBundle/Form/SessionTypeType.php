<?php

namespace Ilios\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SessionTypeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('sessionTypeCssClass')
            ->add('assessment')
            ->add('assessmentOption', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\AssessmentOption"])
            ->add('owningSchool', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\School"])
            ->add('aamcMethods', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\AamcMethod"])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\SessionType'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ilios_corebundle_sessiontype_form_type';
    }
}
