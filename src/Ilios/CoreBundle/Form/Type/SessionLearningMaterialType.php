<?php

namespace Ilios\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SessionLearningMaterialType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('notes', null, ['required' => false])
            ->add('required', null, ['required' => false])
            ->add('publicNotes', null, ['required' => false])
            ->add('session', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Session"
            ])
            ->add('learningMaterial', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:LearningMaterial"
            ])
            ->add('meshDescriptors', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:MeshDescriptor"
            ])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\SessionLearningMaterial'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sessionlearningmaterial';
    }
}
