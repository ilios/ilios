<?php

namespace Ilios\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityManager;

use Ilios\CoreBundle\DataTransformer\SingleRelatedType;
use Ilios\CoreBundle\DataTransformer\ManyRelatedType;

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
            ->add('deleted', null, array('required' => false))
            ->add('externalId', null, array('required' => false))
            ->add('locked', null, array('required' => false))
            ->add('archived', null, array('required' => false))
            ->add('publishedAsTbd', null, array('required' => false))
            ->add('clerkshipType', 'single_related', array(
                'entityName' => 'IliosCoreBundle:CourseClerkshipType',
                'required' => false
            ))
            ->add('owningSchool', 'single_related', array(
                'entityName' => 'IliosCoreBundle:School',
            ))
            ->add('publishEvent', 'single_related', array(
                'entityName' => 'IliosCoreBundle:PublishEvent',
                'required' => false
            ))
            ->add('directors', 'many_related', array(
                'entityName' => 'IliosCoreBundle:User',
                'required' => false
            ))
            ->add('cohorts', 'many_related', array(
                'entityName' => 'IliosCoreBundle:Cohort',
                'required' => false
            ))
            ->add('disciplines', 'many_related', array(
                'entityName' => 'IliosCoreBundle:Discipline',
                'required' => false
            ))
            ->add('objectives', 'many_related', array(
                'entityName' => 'IliosCoreBundle:Objective',
                'required' => false
            ))
            ->add('meshDescriptors', 'many_related', array(
                'entityName' => 'IliosCoreBundle:MeshDescriptor',
                'required' => false
            ))
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
