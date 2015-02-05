<?php

namespace Ilios\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CourseType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('level')
            ->add('year')
            ->add('startDate')
            ->add('endDate')
            ->add('deleted')
            ->add('externalId')
            ->add('locked')
            ->add('archived')
            ->add('publishedAsTbd')
            ->add('clerkshipType', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\CourseClerkshipType"])
            ->add('school', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\School"])
            ->add('publishEvent', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\PublishEvent"])
            ->add('directors', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\User"])
            ->add('cohorts', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\Cohort"])
            ->add('disciplines', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\Discipline"])
            ->add('objectives', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\Objective"])
            ->add('meshDescriptors', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\MeshDescriptor"])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\Course'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ilios_corebundle_course_form_type';
    }
}
