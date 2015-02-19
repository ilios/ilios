<?php

namespace Ilios\CoreBundle\Form;

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
            ->add('competency', 'single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Competency"
            ])
            ->add('courses', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Course"
            ])
            ->add('programYears', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:ProgramYear"
            ])
            ->add('sessions', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Session"
            ])
            ->add('parents', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Objective"
            ])
            ->add('children', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Objective"
            ])
            ->add('meshDescriptors', 'many_related', [
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
