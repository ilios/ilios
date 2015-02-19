<?php

namespace Ilios\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CurriculumInventorySequenceBlockType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('required')
            ->add('childSequenceOrder')
            ->add('orderInSequence')
            ->add('minimum')
            ->add('maximum')
            ->add('track')
            ->add('startDate', 'datetime', array(
            'widget' => 'single_text',
            ))
            ->add('endDate', 'datetime', array(
            'widget' => 'single_text',
            ))
            ->add('duration')
            ->add('academicLevel', 'single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:CurriculumInventoryAcademicLevel"
            ])
            ->add('course', 'single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Course"
            ])
            ->add('parent', 'single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:CurriculumInventorySequenceBlock"
            ])
            ->add('report', 'single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:CurriculumInventoryReport"
            ])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'curriculuminventorysequenceblock';
    }
}
