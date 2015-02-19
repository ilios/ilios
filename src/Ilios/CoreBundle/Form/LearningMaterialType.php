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
            ->add('uploadDate', 'datetime', array(
            'widget' => 'single_text',
            ))
            ->add('originalAuthor')
            ->add('token')
            ->add('userRole', 'single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:LearningMaterialUserRole"
            ])
            ->add('status', 'single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:LearningMaterialStatus"
            ])
            ->add('owningUser', 'single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:User"
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
