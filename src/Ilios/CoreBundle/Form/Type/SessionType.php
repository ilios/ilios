<?php

namespace Ilios\CoreBundle\Form\Type;

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
            ->add('title', null, ['required' => false])
            ->add('attireRequired', null, ['required' => false])
            ->add('equipmentRequired', null, ['required' => false])
            ->add('supplemental', null, ['required' => false])
            ->add('deleted', null, ['required' => false])
            ->add('publishedAsTbd', null, ['required' => false])
            ->add('sessionType', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:SessionType"
            ])
            ->add('course', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Course"
            ])
            ->add('ilmSessionFacet', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:IlmSession"
            ])
            ->add('disciplines', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Discipline"
            ])
            ->add('objectives', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Objective"
            ])
            ->add('meshDescriptors', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:MeshDescriptor"
            ])
            ->add('publishEvent', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:PublishEvent"
            ])
            ->add('sessionDescription', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:SessionDescription"
            ])
            ->add('sessionLearningMaterials', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:SessionLearningMaterial"
            ])
            ->add('instructionHours', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:InstructionHours"
            ])
            ->add('offerings', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Offering"
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
