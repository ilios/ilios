<?php

namespace Ilios\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DisciplineType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('owningSchool', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\School"])
            ->add('courses', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\Course"])
            ->add('programYears', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\ProgramYear"])
            ->add('sessions', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\Session"])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\Discipline'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ilios_corebundle_discipline_form_type';
    }
}
