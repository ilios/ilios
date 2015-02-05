<?php

namespace Ilios\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MeshTermType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('meshTermUid')
            ->add('name')
            ->add('lexicalTag')
            ->add('conceptPreferred')
            ->add('recordPreferred')
            ->add('permuted')
            ->add('print')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('meshConcepts', 'tdn_entity', ['required' => false, 'class' => "Ilios\\CoreBundle\\Entity\\MeshConcept"])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\MeshTerm'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ilios_corebundle_meshterm_form_type';
    }
}
