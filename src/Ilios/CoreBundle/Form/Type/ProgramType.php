<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Form\DataTransformer\RemoveMarkupTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ProgramType
 * @package Ilios\CoreBundle\Form\Type
 */
class ProgramType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, ['required' => false, 'empty_data' => null])
            ->add('shortTitle', null, ['empty_data' => null])
            ->add('duration')
            ->add('deleted', null, ['required' => false])
            ->add('publishedAsTbd', null, ['required' => false])
            ->add('publishEvent', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:PublishEvent"
            ])
            ->add('school', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:School"
            ])
            ->add('programYears', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:ProgramYear"
            ])
            ->add('curriculumInventoryReports', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:CurriculumInventoryReport"
            ])
        ;
        $transformer = new RemoveMarkupTransformer();
        foreach (['title', 'shortTitle'] as $element) {
            $builder->get($element)->addViewTransformer($transformer);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\Program'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'program';
    }
}
