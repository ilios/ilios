<?php

namespace Ilios\CoreBundle\Form\Type;

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
            ->add('id')
            ->add('name')
            ->add('umlsUid')
            ->add('preferred', null, ['required' => false])
            ->add('scopeNote', null, ['required' => false])
            ->add('casn1Name', null, ['required' => false])
            ->add('registryNumber', null, ['required' => false])
            ->add('createdAt')
            ->add('updatedAt')
            ->add('descriptors', 'tdn_many_related', [
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
            'data_class' => 'Ilios\CoreBundle\Entity\MeshConcept'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'meshconcept';
    }
}
