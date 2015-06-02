<?php

namespace Ilios\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProgramType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, ['required' => false])
            ->add('shortTitle')
            ->add('duration')
            ->add('deleted', null, ['required' => false])
            ->add('publishedAsTbd', null, ['required' => false])
            ->add('publishEvent', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:PublishEvent"
            ])
            ->add('owningSchool', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:School"
            ])
            ->add('programYears', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:ProgramYear"
            ])
            ->add('curriculumInventoryReports', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:CurriculumInventoryReport"
            ])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\Program'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'program';
    }
}
