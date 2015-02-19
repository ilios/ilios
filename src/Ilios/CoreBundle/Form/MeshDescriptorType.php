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
            ->add('courses', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Course"
            ])
            ->add('objectives', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Objective"
            ])
            ->add('sessions', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Session"
            ])
            ->add('concepts', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:MeshConcept"
            ])
            ->add('qualifiers', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:MeshQualifier"
            ])
            ->add('sessionLearningMaterials', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:SessionLearningMaterial"
            ])
            ->add('courseLearningMaterials', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:CourseLearningMaterial"
            ])
            ->add('previousIndexing', 'many_related', [
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
