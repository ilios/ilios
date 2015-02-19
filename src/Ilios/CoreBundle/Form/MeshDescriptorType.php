<?php

namespace Ilios\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MeshDescriptorType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id')
            ->add('name')
            ->add('annotation')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('courses', 'multi_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Course"
            ])
            ->add('objectives', 'multi_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Objective"
            ])
            ->add('sessions', 'multi_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Session"
            ])
            ->add('concepts', 'multi_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:MeshConcept"
            ])
            ->add('qualifiers', 'multi_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:MeshQualifier"
            ])
            ->add('sessionLearningMaterials', 'multi_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:SessionLearningMaterial"
            ])
            ->add('courseLearningMaterials', 'multi_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:CourseLearningMaterial"
            ])
            ->add('previousIndexing', 'multi_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:MeshPreviousIndexing"
            ])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\MeshDescriptor'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'meshdescriptor';
    }
}
