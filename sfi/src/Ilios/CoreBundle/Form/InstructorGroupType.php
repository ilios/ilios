<?php

namespace Ilios\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InstructorGroupType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('school', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\School"])
            ->add('groups', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\LearnerGroup"])
            ->add('ilmSessionFacets', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\IlmSessionFacet"])
            ->add('users', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\User"])
            ->add('offerings', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\Offering"])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\InstructorGroup'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ilios_corebundle_instructorgroup_form_type';
    }
}
