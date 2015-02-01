<?php

namespace Ilios\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ObjectiveType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('competency', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\Competency"])
            ->add('courses', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\Course"])
            ->add('programYears', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\ProgramYear"])
            ->add('sessions', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\Session"])
            ->add('parents', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\Objective"])
            ->add('children', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\Objective"])
            ->add('meshDescriptors', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\MeshDescriptor"])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\Objective'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ilios_corebundle_objective_form_type';
    }
}
