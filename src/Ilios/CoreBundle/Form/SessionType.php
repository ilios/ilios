<?php

namespace Ilios\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SessionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('attireRequired')
            ->add('equipmentRequired')
            ->add('supplemental')
            ->add('deleted')
            ->add('publishedAsTbd')
            ->add('updatedAt')
            ->add('sessionType', 'single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:SessionType"
            ])
            ->add('course', 'single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Course"
            ])
            ->add('ilmSessionFacet', 'single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:IlmSessionFacet"
            ])
            ->add('disciplines', 'multi_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Discipline"
            ])
            ->add('objectives', 'multi_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Objective"
            ])
            ->add('meshDescriptors', 'multi_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:MeshDescriptor"
            ])
            ->add('publishEvent', 'single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:PublishEvent"
            ])
            ->add('sessionDescription', 'single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:SessionDescription"
            ])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\Session'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'session';
    }
}
