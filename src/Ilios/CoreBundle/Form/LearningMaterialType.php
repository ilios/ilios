<?php

namespace Ilios\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LearningMaterialType extends AbstractType
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
            ->add('uploadDate')
            ->add('originalAuthor', null, ['required' => false])
            ->add('token', null, ['required' => false])
            ->add('userRole', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:LearningMaterialUserRole"
            ])
            ->add('status', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:LearningMaterialStatus"
            ])
            ->add('owningUser', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:User"
            ])
            ->add('sessionLearningMaterials', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:SessionLearningMaterial"
            ])
            ->add('courseLearningMaterials', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:CourseLearningMaterial"
            ])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\LearningMaterial'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'learningmaterial';
    }
}
