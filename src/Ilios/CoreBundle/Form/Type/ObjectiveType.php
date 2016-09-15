<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Form\Type\AbstractType\ManyRelatedType;
use Ilios\CoreBundle\Form\Type\AbstractType\PurifiedTextareaType;
use Ilios\CoreBundle\Form\Type\AbstractType\SingleRelatedType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ObjectiveType
 * @package Ilios\CoreBundle\Form\Type
 */
class ObjectiveType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', PurifiedTextareaType::class)
            ->add('competency', SingleRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:Competency"
            ])
            ->add('courses', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:Course"
            ])
            ->add('programYears', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:ProgramYear"
            ])
            ->add('sessions', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:Session"
            ])
            ->add('parents', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:Objective"
            ])
            ->add('children', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:Objective"
            ])
            ->add('meshDescriptors', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:MeshDescriptor"
            ])
            ->add('ancestor', SingleRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:Objective"
            ])
            ->add('descendants', ManyRelatedType::class, [
                'required' => false,
                'entityName' => "IliosCoreBundle:Objective"
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\Objective'
        ));
    }
}
