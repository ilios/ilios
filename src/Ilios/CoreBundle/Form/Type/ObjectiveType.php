<?php

namespace Ilios\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ObjectiveType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('competency', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Competency"
            ])
            ->add('courses', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Course"
            ])
            ->add('programYears', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:ProgramYear"
            ])
            ->add('sessions', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Session"
            ])
            ->add('parents', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Objective"
            ])
            ->add('children', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Objective"
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
            'data_class' => 'Ilios\CoreBundle\Entity\Objective'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'objective';
    }
}
