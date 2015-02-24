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
            ->add('annotation', null, ['required' => false])
            ->add('createdAt')
            ->add('updatedAt')
            ->add('courses', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Course"
            ])
            ->add('objectives', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Objective"
            ])
            ->add('sessions', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Session"
            ])
            ->add('concepts', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:MeshConcept"
            ])
            ->add('qualifiers', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:MeshQualifier"
            ])
            ->add('sessionLearningMaterials', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:SessionLearningMaterial"
            ])
            ->add('courseLearningMaterials', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:CourseLearningMaterial"
            ])
            ->add('previousIndexing', 'tdn_single_related', [
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
