<?php

namespace Ilios\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LearnerGroupType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('location')
            ->add('instructors')
            ->add('cohort', 'single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Cohort"
            ])
            ->add('parent', 'single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:LearnerGroup"
            ])
            ->add('ilmSessions', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:IlmSessionFacet"
            ])
            ->add('offerings', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Offering"
            ])
            ->add('instructorGroups', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:InstructorGroup"
            ])
            ->add('users', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:User"
            ])
            ->add('instructorUsers', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:User"
            ])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\LearnerGroup'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'learnergroup';
    }
}
