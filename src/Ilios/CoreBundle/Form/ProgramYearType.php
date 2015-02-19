<?php

namespace Ilios\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProgramYearType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startYear')
            ->add('deleted')
            ->add('locked')
            ->add('archived')
            ->add('publishedAsTbd')
            ->add('program', 'single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Program"
            ])
            ->add('cohort', 'single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Cohort"
            ])
            ->add('directors', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:User"
            ])
            ->add('competencies', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Competency"
            ])
            ->add('disciplines', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Discipline"
            ])
            ->add('objectives', 'many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Objective"
            ])
            ->add('publishEvent', 'single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:PublishEvent"
            ])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\ProgramYear'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'programyear';
    }
}
