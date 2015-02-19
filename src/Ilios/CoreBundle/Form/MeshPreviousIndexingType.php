<?php

namespace Ilios\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MeshPreviousIndexingType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('previousIndexing')
            ->add('descriptor', 'single_related', [
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
            'data_class' => 'Ilios\CoreBundle\Entity\MeshPreviousIndexing'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'meshpreviousindexing';
    }
}
