<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Form\DataTransformer\RemoveMarkupTransformer;
use Ilios\CoreBundle\Form\Type\AbstractType\ManyRelatedType;
use Ilios\CoreBundle\Form\Type\AbstractType\SingleRelatedType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MeshDescriptorType
 * @package Ilios\CoreBundle\Form\Type
 */
class MeshDescriptorType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', null, ['empty_data' => null])
            ->add('name', null, ['empty_data' => null])
            ->add('annotation', null, ['required' => false, 'empty_data' => null])
            ->add('courses', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:Course"
            ])
            ->add('objectives', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:Objective"
            ])
            ->add('sessions', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:Session"
            ])
            ->add('sessionLearningMaterials', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:SessionLearningMaterial"
            ])
            ->add('courseLearningMaterials', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:CourseLearningMaterial"
            ])
            ->add('previousIndexing', SingleRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:MeshPreviousIndexing"
            ])
            ->add('qualifiers', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:MeshQualifier"
            ])
            ->add('concepts', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:MeshConcept"
            ])
        ;
        $transformer = new RemoveMarkupTransformer();
        foreach (['id', 'name', 'annotation'] as $element) {
            $builder->get($element)->addViewTransformer($transformer);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\MeshDescriptor'
        ));
    }
}
