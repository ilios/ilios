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
            ->add('startDate', 'datetime', array(
                'widget' => 'single_text',
            ))
            ->add('endDate', 'datetime', array(
            'widget' => 'single_text',
            ))
            ->add('deleted')
            ->add('externalId')
            ->add('locked')
            ->add('archived')
            ->add('publishedAsTbd')
            ->add('clerkshipType', 'single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:CourseClerkshipType"
            ])
            ->add('owningSchool', 'single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:School"
            ])
            ->add('publishEvent', 'single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:PublishEvent"
            ])
            ->add('directors', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:User"
            ])
            ->add('cohorts', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Cohort"
            ])
            ->add('disciplines', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Discipline"
            ])
            ->add('objectives', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Objective"
            ])
            ->add('meshDescriptors', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:MeshDescriptor"
            ])
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
        return 'course';
    }
}
