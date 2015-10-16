<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Form\DataTransformer\RemoveMarkupTransformer;
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
            ->add('description', null, ['required' => false])
            ->add('required', null, ['required' => false])
            ->add('childSequenceOrder', null, ['required' => false])
            ->add('orderInSequence')
            ->add('minimum')
            ->add('maximum')
            ->add('track', null, ['required' => false])
            ->add('startDate', 'datetime', array(
                'widget' => 'single_text',
            ))
            ->add('endDate', 'datetime', array(
                'widget' => 'single_text',
            ))
            ->add('duration')
            ->add('academicLevel', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:CurriculumInventoryAcademicLevel"
            ])
            ->add('course', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Course"
            ])
            ->add('parent', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:CurriculumInventorySequenceBlock"
            ])
            ->add('children', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:CurriculumInventorySequenceBlock"
            ])
            ->add('report', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:CurriculumInventoryReport"
            ])
            ->add('sessions', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:CurriculumInventorySequenceBlockSession"
            ])
        ;
        $transformer = new RemoveMarkupTransformer();
        foreach (['title', 'description'] as $element) {
            $builder->get($element)->addViewTransformer($transformer);
        }
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
