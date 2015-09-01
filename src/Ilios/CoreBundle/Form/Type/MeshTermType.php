<?php

namespace Ilios\CoreBundle\Form\Type;

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
            ->add('name')
            ->add('meshTermUid')
            ->add('lexicalTag')
            ->add('conceptPreferred')
            ->add('recordPreferred')
            ->add('permuted')
            ->add('printable')
            ->add('concepts', 'tdn_many_related', [
                'entityName' => "IliosCoreBundle:MeshConcept"
            ])
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
        return 'meshterm';
    }
}
