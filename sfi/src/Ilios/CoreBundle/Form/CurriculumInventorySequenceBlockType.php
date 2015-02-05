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
            ->add('startDate')
            ->add('endDate')
            ->add('duration')
            ->add('academicLevel', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\CurriculumInventoryAcademicLevel"])
            ->add('course', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\Course"])
            ->add('parent', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\CurriculumInventorySequenceBlock"])
            ->add('report', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\CurriculumInventoryReport"])
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
        return 'ilios_corebundle_curriculuminventorysequenceblock_form_type';
    }
}
