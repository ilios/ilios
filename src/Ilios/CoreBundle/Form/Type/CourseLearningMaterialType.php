<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Form\Type\AbstractType\ManyRelatedType;
use Ilios\CoreBundle\Form\Type\AbstractType\PurifiedTextareaType;
use Ilios\CoreBundle\Form\Type\AbstractType\SingleRelatedType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CourseLearningMaterialType
 * @package Ilios\CoreBundle\Form\Type
 */
class CourseLearningMaterialType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('notes', PurifiedTextareaType::class, ['required' => false])
            ->add('required', null, ['required' => false])
            ->add('publicNotes', null, ['required' => false])
            ->add('course', SingleRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:Course"
            ])
            ->add('learningMaterial', SingleRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:LearningMaterial"
            ])
            ->add('meshDescriptors', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:MeshDescriptor"
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\CourseLearningMaterial'
        ));
    }
}
