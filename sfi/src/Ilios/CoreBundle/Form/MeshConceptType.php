<?php

namespace Ilios\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MeshConceptType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('meshConceptUid')
            ->add('name')
            ->add('umlsUid')
            ->add('preferred')
            ->add('scopeNote')
            ->add('casn1Name')
            ->add('registryNumber')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('meshTerms', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\MeshTerm"])
            ->add('descriptors', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\MeshDescriptor"])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\MeshConcept'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ilios_corebundle_meshconcept_form_type';
    }
}
