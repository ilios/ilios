<?php

namespace Ilios\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProgramYearType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startYear')
            ->add('deleted')
            ->add('locked')
            ->add('archived')
            ->add('publishedAsTbd')
            ->add('program', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\Program"])
            ->add('cohort', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\Cohort"])
            ->add('directors', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\User"])
            ->add('competencies', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\Competency"])
            ->add('disciplines', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\Discipline"])
            ->add('objectives', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\Objective"])
            ->add('publishEvent', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\PublishEvent"])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\ProgramYear'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ilios_corebundle_programyear_form_type';
    }
}
