<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Form\DataTransformer\RemoveMarkupTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CurriculumInventoryAcademicLevelType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description', null, ['required' => false])
            ->add('level')
            ->add('report', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:CurriculumInventoryReport"
            ])
            ->add('sequenceBlocks', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:CurriculumInventorySequenceBlock"
            ])
        ;
        $transformer = new RemoveMarkupTransformer();
        foreach (['name', 'description'] as $element) {
            $builder->get($element)->addViewTransformer($transformer);
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevel'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'curriculuminventoryacademiclevel';
    }
}
